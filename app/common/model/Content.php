<?php

namespace app\common\model;


/**
 * 内容模型.
 */
class Content extends BaseModel
{
    // 表名
    protected $name = 'content';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function onBeforeInsert($model)
    {
        //置顶&置顶时间
        $model->top = 0;
        $model->expiry_time = 0;
        if ($model->top_id) {
            $model->expiry_time = (new TopConfig())->getExpiryTimeById($model->top_id);
            $model->top = 1;
        }
    }

    public function setPicturesAttr($value)
    {
        $pictures = [];
        foreach ($value as $pic) {
            $pictures[] = [
                'key' => $pic['key'],
                'imageInfo' => $pic['imageInfo']
            ];
        }
        return json_encode($pictures, JSON_UNESCAPED_UNICODE);
    }


    public function getPicturesAttr($value)
    {
        return json_decode($value, true);
    }

    public function getById(int $id, int $uid): array
    {
        $detail = $this->field([
            'id', 'uid', 'content', 'pictures', 'like_count', 'contacts', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'top', 'create_time', 'expiry_time'
        ])->where('id', $id)->where('status', 1)->find();
        if ($detail) {
            $detail = $detail->toArray();
            $detail = $this->formatMultiValue([$detail], $uid);
            return current($detail);
        }
        return [];
    }

    public function viewInc(int $cid)
    {
        $this->where('id', $cid)->inc('view_count', 1)->update();
    }

    public function likeInc(int $cid)
    {
        $this->where('id', $cid)->inc('like_count', 1)->update();
    }

    public function likeDec(int $cid)
    {
        $this->where('id', $cid)->where('like_count', '>=', '1')->dec('like_count', 1)->update();
    }

    /**
     * 首页列表数据
     *
     * @param integer $uid
     * @param array $condition
     * @param integer $page
     * @param integer $pageSize
     * @return array
     * @author xsm
     * @since 2020-10-02
     */
    public function getHomeList(int $uid, array $condition, int $page, int $pageSize): array
    {
        if ($condition['column_id']) {
            $columnContent = (new ColumnContent())->getHomeList($condition, $page, $pageSize);
            $cids = [];
            if ($columnContent) {
                $cids = array_unique(array_column($columnContent, 'cid'));
            }
            $lists = $this->getByCids($cids, $uid);
        } else {
            $topList = $this->getTopList( $condition, $page, $pageSize);
            $diff = $pageSize - count($topList);
            $lists = [];
            if ($diff > 0) {
                $where = [
                    'pay_status' => 1,
                    'expiry_time' => ['expiry_time', '<', time()],
                    'status' => 1,
                ];
                if (isset($condition['type']) && !empty($condition['type'])) {
                    $where['type'] = intval($condition['type']);
                }
                if (isset($condition['keyword']) && !empty($condition['keyword'])) {
                    $where['content'] = ['content', 'like', "%{$condition['keyword']}%"];
                }
                $list = $this->getList($where,  ['update_time' => 'desc'], $page, $diff);
            }
            $lists = array_merge($topList, $list);
        }
        $lists = $this->formatMultiValue($lists, $uid);
        return $lists;
    }

    /**
     * 附近的消息
     *
     * @param integer $uid
     * @param array $condition
     * @param integer $page
     * @param integer $pageSize
     * @return array
     * @author xsm
     * @since 2020-10-10
     */
    public function getNearBy(int $uid, array $condition, int $page, int $pageSize): array
    {
        $where = [
            'pay_status' => 1,
            'status' => 1,
        ];
        if (isset($condition['type']) && !empty($condition['type'])) {
            $where['type'] = intval($condition['type']);
        }
        if (isset($condition['geohash']) && !empty($condition['geohash'])) {
            $where['geohash'] = ['geohash', 'like', "{$condition['geohash']}%"];
        }
        $lists = $this->getList($where,  ['update_time' => 'desc'], $page, $pageSize);
        $lists = $this->formatMultiValue($lists, $uid);
        //移除置顶标签
        foreach($lists as &$list){
            if(isset($list['tags'][0]) && $list['tags'][0] == '置顶'){
                array_shift($list['tags']);
            }
        }
        return $lists;
    }


    /**
     * 搜索
     *
     * @param integer $uid
     * @param integer $columnId
     * @param string $keyword
     * @param integer $page
     * @param integer $pageSize
     * @return void
     * @author xsm
     * @since 2020-10-24
     */
    public function getListByFullIndex(int $uid,int $columnId, string $keyword, int $page, int $pageSize)
    {
        $offset = ($page - 1) * $pageSize;
        // $limit = "{$offset},{$pageSize}";
    //     $sql = "SELECT id FROM ns_content WHERE FIND_IN_SET({$columnId},column_ids) AND
    //                 MATCH (content)
    // AGAINST ('{$keyword}') LIMIT {$limit}";
    // echo $sql;exit();
        $lists = $this->whereRaw("FIND_IN_SET({$columnId},column_ids) AND pay_status=1 AND status=1 AND MATCH (content) AGAINST ('{$keyword}')")->select()->toArray();
        $lists = (new Content())->formatMultiValue($lists, $uid);
        return $lists;
    }

    public function getByCids(array $cids): array
    {
        if (empty($cids)) {
            return [];
        }
        $cids = join(',', $cids);
        $lists = $this->field([
            'id', 'uid', 'content', 'pictures', 'like_count', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'top', 'create_time', 'expiry_time'
        ])->where('id', 'in', $cids)
            ->order('top', 'desc')
            ->order('update_time', 'desc')
            ->select()->toArray();
        if (empty($lists)) {
            return [];
        }
        // $lists = $this->formatMultiValue($lists, $uid);

        return $lists;
    }

    /**
     * 获取置顶数据
     *
     * @param integer $uid
     * @param array $condition
     * @param integer $page
     * @param integer $pageSize
     * @return array
     * @author xsm
     * @since 2020-10-24
     */
    public function getTopList(array $condition, int $page, int $pageSize): array
    {
        $where = [
            'pay_status' => 1,
            'top' => 1,
            'expiry_time' => ['expiry_time', '>', time()],
            'status' => 1,
        ];
        if (isset($condition['type']) && !empty($condition['type'])) {
            $where['type'] = intval($condition['type']);
        }
        if (isset($condition['keyword']) && !empty($condition['keyword'])) {
            $where['content'] = ['content', 'like', "%{$condition['keyword']}%"];
        }
        $list = $this->getList($where, ['update_time' => 'desc'], $page, $pageSize);
        return $list;
    }

    public function getMyPost(int $uid, array $condition, array $order, int $page, int $pageSize): array
    {
       $lists = $this->getList($condition,$order,$page,$pageSize);
       $lists = $this->formatMultiValue($lists, $uid);
       return $lists;
    }

    public function getList(array $condition, array $order, int $page, int $pageSize): array
    {
        $where = [];
        if (isset($condition['uid']) && !empty($condition['uid'])) {
            $where[] = ['uid', '=', intval($condition['uid'])];
        }
        if (isset($condition['status']) && is_numeric($condition['status'])) {
            $where[] = ['status', '=', intval($condition['status'])];
        }
        if (isset($condition['pay_status']) && is_numeric($condition['pay_status'])) {
            $where[] = ['pay_status', '=', intval($condition['pay_status'])];
        }
        if (isset($condition['top']) && is_numeric($condition['top'])) {
            $where[] = ['top', '=', intval($condition['top'])];
        }
        if (isset($condition['type']) && !empty($condition['type'])) {
            $where[] = ['type', '=', intval($condition['type'])];
        }
        if (isset($condition['keyword'])) {
            $where[] = $condition['keyword'];
        }
        if (isset($condition['expiry_time'])) {
            $where[] = $condition['expiry_time'];
        }
        if (isset($condition['geohash'])) {
            $where[] = $condition['geohash'];
        }
        $offset = ($page - 1) * $pageSize;
        $lists = $this->field([
            'id', 'uid', 'content', 'pictures', 'like_count','contacts', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'create_time', 'top', 'expiry_time'
        ])->where($where)->order($order)->limit($offset, $pageSize)
            ->select()->toArray();
        if (empty($lists)) {
            return [];
        }
        // $lists = $this->formatMultiValue($lists, $uid);

        return $lists;
    }


    /**
     * 格式化数据
     *
     * @param array $lists
     * @param integer $uid
     * @return array
     * @author xsm
     * @since 2020-10-24
     */
    public function formatMultiValue(array $lists, int $uid): array
    {
        $config = get_addon_config('cloudstore');
        $qiniuDomain = $config['domain'];
        $targetIds = $uids = [];
        foreach ($lists as &$data) {
            $targetIds[] = $data['id'];
            $uids[] = $data['uid'];
            $data['liked'] = false;
            if (isset($data['pictures']) && !empty($data['pictures'])) {
                foreach ($data['pictures'] as &$pic) {
                    $url = $qiniuDomain . '/' . $pic['key'];
                    $pic['thumbnailUrl'] = "{$url}?imageMogr2/auto-orient/thumbnail/300x2000%3E/quality/70/interlace/1";
                    $pic['smallPicUrl'] = "{$url}?imageMogr2/auto-orient/thumbnail/400x2000%3E/quality/70/interlace/1";
                    $pic['middlePicUrl'] = "{$url}?imageMogr2/auto-orient/thumbnail/1500x2000%3E/quality/70/interlace/1";
                }
            }
            if (isset($data['create_time']) && !empty($data['create_time'])) {
                $data['create_time_text'] = date('Y-m-d H:i', $data['create_time']);
            }
            $data['tags'] = [];
            if (isset($data['top']) && $data['top'] && $data['expiry_time'] > time()) {
                $data['tags'][] = '置顶';
            }
        }
        //是否点赞
        if ($uid) {
            $likeList = (new LikeLog())->getUserLikeContentByTargetId($uid, $targetIds, LikeLog::CONTENT_LIKE_TYPE);
            if ($likeList) {
                $likeList = array_column($likeList, null, 'target_id');
                foreach ($lists as &$list) {
                    if (isset($likeList[$list['id']])) {
                        $list['liked'] = true;
                    }
                }
                unset($list);
            }
        }
        $uids = array_unique($uids);
        $cids = array_column($lists, 'id');
        //内容关联的标签
        $columnInfoList = (new ColumnContent())->getRelateColumnListByCids($cids);
        //获取用户信息
        $users = (new Wxuser())->getUserByUids($uids);
        $users = array_column($users, null, 'uid');
        foreach ($lists as &$list) {
            if (isset($columnInfoList[$list['id']])) {
                $list['tags'] = array_merge($list['tags'], array_column($columnInfoList[$list['id']], 'name'));
            }
            $list['user'] = [];
            if (isset($users[$list['uid']])) {
                $list['user'] = $users[$list['uid']];
            }
        }
        return $lists;
    }

    /**
     * 改变内容支付状态
     *
     * @param integer $cid
     * @param integer $status
     * @return void
     * @author xsm
     * @since 2020-09-20
     */
    public function changePayStatus(int $cid, int $status)
    {
        $this->where('id', $cid)->save([
            'pay_status' => $status
        ]);
        ColumnContent::where('cid', $cid)->save([
            'pay_status' => $status
        ]);
    }
}
