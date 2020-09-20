<?php

namespace app\common\model;


/**
 * 内容模型.
 */
class Comment extends BaseModel
{
    // 表名
    protected $name = 'comment';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';




    public function getById(int $id): array
    {
        $detail = $this->field([
            'id', 'uid', 'content', 'pictures', 'like_count', 'mobile', 'share_count', 'comment_count',
            'view_count', 'address', 'lng', 'lat', 'create_time'
        ])->where('id', $id)->where('status', 1)->find();
        if ($detail) {
            $detail = $detail->toArray();
            $detail = $this->formatValue($detail);
            $user = (new Wxuser())->getUserByUid($detail['uid']);
            $detail['user'] = $user;
            return $detail;
        }
        return [];
    }


    public function getList(array $condition, int $page, int $pageSize): array
    {
        $where = [];
        if (isset($condition['cid']) && !empty($condition['cid'])) {
            $where[] = ['cid', '=', intval($condition['cid'])];
        }
        $offset = ($page - 1) * $pageSize;
        $lists = $this->field([
            'id', 'uid', 'cid', 'content', 'create_time'
        ])->where($where)->order('update_time', 'desc')->limit($offset, $pageSize)
            ->select()->toArray();
        $uids = [];
        foreach ($lists as &$list) {
            $list = $this->formatValue($list);
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

    public function formatValue(array $data)
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
        return $data;
    }
}
