<?php

namespace app\common\model;

use think\model\concern\SoftDelete;
use GuzzleHttp\Client;
use EasyWeChat\Factory;
use think\facade\{Config, Log};
use Geohash;

/**
 * 内容模型.
 */
class Content extends BaseModel
{
    use SoftDelete;
    // 表名
    protected $name = 'content';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    public static function onBeforeInsert($model)
    {
        //置顶&计算置顶时间
        $model->top = 0;
        $model->expiry_time = 0;
        if ($model->top_id) {
            $model->expiry_time = (new TopConfig())->getExpiryTimeById($model->top_id);
            $model->top = 1;
        }
        $model->geohash = (new Geohash())->encode($model->lat, $model->lng);
    }

    public static function onBeforeUpdate($model)
    { 
        $model->geohash = (new Geohash())->encode($model->lat, $model->lng);
    }

    public function updateExpiryTimeByCid(int $cid, int $topId)
    {
        $expiryTime = (new TopConfig())->getExpiryTimeById($topId);
        if ($expiryTime) {
            $this->addExpiryTime($cid, $expiryTime);
            $this->changeTopStatus($cid, 1);
        }
    }

    public function setPicturesAttr($value)
    {
        $pictures = [];
        foreach ($value as $pic) {
            $pictures[] = [
                'key' => $pic['key'],
                'imageInfo' => $pic['imageInfo'] ?? []
            ];
        }
        return json_encode($pictures, JSON_UNESCAPED_UNICODE);
    }


    public function getPicturesAttr($value)
    {
        return json_decode($value, true);
    }

    public function getBaseById(int $cid): array
    {
        $detail = $this->field([
            'id', 'uid', 'content', 'pictures', 'like_count', 'contacts', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'top', 'create_time', 'expiry_time', 'extra'
        ])->where('id', $cid)->find(); //->where('status', 1)
        if (empty($detail)) {
            return [];
        }
        $detail = $detail->toArray();
        $detail['extra'] = json_decode($detail['extra'], true);
        return $detail;
    }

    public function getById(int $cid, int $uid): array
    {
        $detail = $this->getBaseById($cid);
        if ($detail) {
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
            $topList = $this->getTopList($condition, $page, $pageSize);
            $diff = $pageSize - count($topList);
            $lists = [];
            if ($diff > 0) {
                $where = [
                    'pay_status' => 1,
                    'expiry_time' => ['expiry_time', '<', time()],
                    'status' => 1,
                    'is_online' => 1,
                ];
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
            'is_online' => 1,
        ];
        if (isset($condition['geohash']) && !empty($condition['geohash'])) {
            $where['geohash'] = ['geohash', 'like', "{$condition['geohash']}%"];
        }
        $lists = $this->getList($where,  ['update_time' => 'desc'], $page, $pageSize);
        $lists = $this->formatMultiValue($lists, $uid);
        //移除置顶标签
        foreach ($lists as &$list) {
            if (isset($list['tags'][0]) && $list['tags'][0] == '置顶') {
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
    public function getListByFullIndex(int $uid, int $columnId, string $keyword, int $page, int $pageSize)
    {
        // $offset = ($page - 1) * $pageSize;
        // $limit = "{$offset},{$pageSize}";
        //     $sql = "SELECT id FROM ns_content WHERE FIND_IN_SET({$columnId},column_ids) AND
        //                 MATCH (content)
        // AGAINST ('{$keyword}') LIMIT {$limit}";
        // echo $sql;exit();
        $whereArr = ['pay_status=1', "MATCH (content) AGAINST ('{$keyword}')"];
        if ($columnId) {
            $whereArr[] = "FIND_IN_SET({$columnId},column_ids)";
        }
        $whereRaw = join(' AND ', $whereArr);
        $lists = $this->whereRaw($whereRaw)->select()->toArray();
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
            'id', 'uid', 'content', 'contacts', 'pictures', 'like_count', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'top', 'create_time', 'expiry_time'
        ])->where('id', 'in', $cids)
            ->order('top', 'desc')
            ->order('update_time', 'desc')
            ->select()->toArray();
        if (empty($lists)) {
            return [];
        }
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
            'is_online' => 1,
        ];
        if (isset($condition['keyword']) && !empty($condition['keyword'])) {
            $where['content'] = ['content', 'like', "%{$condition['keyword']}%"];
        }
        $list = $this->getList($where, ['update_time' => 'desc'], $page, $pageSize);
        return $list;
    }

    public function getMyPost(int $uid, array $condition, array $order, int $page, int $pageSize): array
    {
        $lists = $this->getList($condition, $order, $page, $pageSize);
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
        if (isset($condition['is_online']) && is_numeric($condition['is_online'])) {
            $where[] = ['is_online', '=', intval($condition['is_online'])];
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
            'id', 'uid', 'content', 'pictures', 'like_count', 'contacts', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'create_time','update_time','top', 'expiry_time'
        ])->where($where)->order($order)->limit($offset, $pageSize)
            ->select()->toArray();
        if (empty($lists)) {
            return [];
        }
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
                    $pic['url'] = "{$url}?imageMogr2/auto-orient/thumbnail/300x2000%3E/quality/70/interlace/1";
                    $pic['thumbnailUrl'] = "{$url}?imageMogr2/auto-orient/thumbnail/300x2000%3E/quality/70/interlace/1";
                    $pic['smallPicUrl'] = "{$url}?imageMogr2/auto-orient/thumbnail/400x2000%3E/quality/70/interlace/1";
                    $pic['middlePicUrl'] = "{$url}?imageMogr2/auto-orient/thumbnail/1500x2000%3E/quality/70/interlace/1";
                    $pic['imageInfo'] = $pic['imageInfo'] ?? [];
                }
            }
            if (isset($data['create_time']) && !empty($data['create_time'])) {
                $data['create_time_text'] = date('Y-m-d H:i', $data['create_time']);
            }
            if (isset($data['update_time']) && !empty($data['update_time'])) {
                $data['update_time_text'] = date('Y-m-d H:i', $data['update_time']);
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
            'pay_status' => $status,
            'update_time' => time(),
        ]);
        ColumnContent::where('cid', $cid)->save([
            'pay_status' => $status,
        ]);
    }

    /**
     * 改变内容置顶状态
     *
     * @param integer $cid
     * @param integer $status
     * @return void
     * @author xsm
     * @since 2020-09-20
     */
    public function changeTopStatus(int $cid, int $status)
    {
        $this->where('id', $cid)->save([
            'top' => $status,
            'update_time' => time(),
        ]);
        ColumnContent::where('cid', $cid)->save([
            'top' => $status,
        ]);
    }

    /**
     * 延迟过期时间
     *
     * @param integer $cid
     * @param integer $expiryTime
     * @return void
     * @author xsm
     * @since 2020-09-20
     */
    public function addExpiryTime(int $cid, int $expiryTime)
    {
        $this->where('id', $cid)->save([
            'expiry_time' => $expiryTime
        ]);
        ColumnContent::where('cid', $cid)->save([
            'expiry_time' => $expiryTime,
            'top' => 1,
        ]);
    }

    /**
     * 敏感内容过滤
     *
     * @param string $accessToken
     * @param string $content
     * @return void
     * @author xsm
     * @since 2020-07-17
     */
    public function msgSecCheck(string $content): array
    {

        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token={$accessToken}";
        $data = json_encode(['content' => $content], JSON_UNESCAPED_UNICODE);
        try {
            $client = new Client();
            $response = $client->post($url, ['body' => $data]);
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
        $log = 'msgSecCheck:' . $response->getBody();
        Log::record($log);
        $res = json_decode($response->getBody(), true);
        if (87014 == $res['errcode']) {
            return ['code' => 1, 'msg' => '含有违法违规内容'];
        }
        return ['code' => 0, 'msg' => 'ok'];
    }

    // public function imgSecCheck($fileUrl)
    // {
    //     $accessToken = $this->getAccessToken();
    //     $url = "https://api.weixin.qq.com/wxa/img_sec_check?access_token={$accessToken}";
    //     $media = curl_file_create($fileUrl, 'image/jpeg');
    //     $client = new Client();
    //     $response = $client->post($url, ['body' => ['media' => $media]]);
    //     return json_decode($response->getBody(), true);
    // }

    public function getAccessToken(): string
    {
        $config = Config::get('api.miniprogram.ns');
        $app = Factory::officialAccount($config);
        $accessToken = $app->access_token;
        $token = $accessToken->getToken(true);
        return $token['access_token'] ?? '';
    }
}
