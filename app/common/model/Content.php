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
        //置顶
        if ($model->top_id) {
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

    public function add(int $uid, array $data)
    {
    }

    public function getHomeList(array $condition, int $page, int $pageSize): array
    {
        $topList = $this->getTopList($condition, $page, $pageSize);
        $diff = $pageSize - count($topList);
        if ($diff > 0) {
            $where = [
                ['pay_status', '=', 1],
                ['expiry_time', '<', time()],
                ['status', '=', 1],
            ];
            $list = $this->getList($where, $page, $diff);
        }
        $list = array_merge($topList, $list);
        return $list;
    }

    public function getTopList(array $condition, int $page, int $pageSize): array
    {
        $where = [
            ['pay_status', '=', 1],
            ['top', '=', 1],
            ['expiry_time', '>', time()],
            ['status', '=', 1],
        ];
        if (isset($condition['type'])) {
        }
        $list = $this->getList($where, $page, $pageSize);
        return $list;
    }

    public function getList(array $where, int $page, int $pageSize): array
    {
        $offset = ($page - 1) * $pageSize;
        $lists = $this->field([
            'id', 'uid','content', 'pictures', 'like_count', 'mobile', 'share_count', 'comment_count',
            'address', 'lng', 'lat', 'create_time'
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
        if(isset($data['create_time']) && !empty($data['create_time'])){
            $data['create_time_text'] = date('Y-m-d H:i',$data['create_time']);
        }
        return $data;
    }
}
