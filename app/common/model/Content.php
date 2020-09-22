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

    public function getById(int $id): array
    {
        $detail = $this->field([
            'id', 'uid', 'content', 'pictures', 'like_count', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'create_time'
        ])->where('id', $id)->where('status', 1)->find();
        if ($detail) {
            $detail = $detail->toArray();
            $detail = $this->formatValue($detail);
            $columnInfoList = (new ColumnContent())->getRelateColumnListByCids([$detail['id']]);
            $detail['tags'] = array_merge($detail['tags'], array_column($columnInfoList[$detail['id']], 'name'));
            $user = (new Wxuser())->getUserByUid($detail['uid']);
            $detail['user'] = $user;
            return $detail;
        }
        return [];
    }

    public function viewInc(int $cid)
    {
        $this->where('id', $cid)->inc('view_count', 1)->update();
    }

    /**
     * 首页列表数据
     *
     * @param array $condition
     * @param integer $page
     * @param integer $pageSize
     * @return array
     * @author xsm
     * @since 2020-09-20
     */
    public function getHomeList(array $condition, int $page, int $pageSize): array
    {
        $columnContent = (new ColumnContent())->getHomeList($condition, $page, $pageSize);
        $cids = [];
        if ($columnContent) {
            $cids = array_unique(array_column($columnContent, 'cid'));
        }
        $list = $this->getByCids($cids);
        return $list;
    }

    public function getByCids(array $cids): array
    {
        if (empty($cids)) {
            return [];
        }
        $columnInfoList = (new ColumnContent())->getRelateColumnListByCids($cids);
        $cids = join(',', $cids);
        $lists = $this->field([
            'id', 'uid', 'content', 'pictures', 'like_count', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'top', 'create_time'
        ])->where('id', 'in', $cids)
            ->order('top', 'desc')
            ->order('update_time', 'desc')
            ->select()->toArray();
        if (empty($lists)) {
            return [];
        }
        $uids = [];
        foreach ($lists as &$list) {
            $list = $this->formatValue($list);
            $list['tags'] = array_merge($list['tags'], array_column($columnInfoList[$list['id']], 'name'));
            $uids[] = $list['uid'];
        }
        unset($list);
        $uids = array_unique($uids);
        $users = (new Wxuser())->getUserByUids($uids);
        $users = array_column($users, null, 'uid');
        foreach ($lists as &$list) {
            $list['user'] = [];
            if (isset($users[$list['uid']])) {
                $list['user'] = $users[$list['uid']];
            }
        }
        return $lists;
    }

    public function getList(array $condition, int $page, int $pageSize): array
    {
        $where = [];
        if (isset($condition['uid']) && !empty($condition['uid'])) {
            $where['uid'] =  intval($condition['uid']);
        }
        if (isset($condition['status']) && is_numeric($condition['status'])) {
            $where['status'] = intval($condition['status']);
        }
        if (isset($condition['pay_status']) && is_numeric($condition['pay_status'])) {
            $where['pay_status'] = intval($condition['pay_status']);
        }
        $offset = ($page - 1) * $pageSize;
        $lists = $this->field([
            'id', 'uid', 'content', 'pictures', 'like_count', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'create_time', 'top'
        ])->where($where)->order('update_time', 'desc')->limit($offset, $pageSize)
            ->select()->toArray();
        if (empty($lists)) {
            return [];
        }
        $uids = [];
        foreach ($lists as &$list) {
            $list = $this->formatValue($list);
            $uids[] = $list['uid'];
        }
        unset($list);
        $uids = array_unique($uids);
        $cids = array_column($lists, 'id');
        $columnInfoList = (new ColumnContent())->getRelateColumnListByCids($cids);
        //获取用户信息
        $users = (new Wxuser())->getUserByUids($uids);
        $users = array_column($users, null, 'uid');
        foreach ($lists as &$list) {
            $list['tags'] = array_merge($list['tags'], array_column($columnInfoList[$list['id']], 'name'));
            $list['user'] = [];
            if (isset($users[$list['uid']])) {
                $list['user'] = $users[$list['uid']];
            }
        }
        return $lists;
    }


    public function formatValue(array $data): array
    {
        $config = get_addon_config('cloudstore');
        $qiniuDomain = $config['domain'];
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
        if (isset($data['top']) && $data['top']) {
            $data['tags'][] = '置顶';
        }

        return $data;
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
