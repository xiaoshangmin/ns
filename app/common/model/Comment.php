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



    /**
     * 回复我的评论
     *
     * @param integer $uid
     * @param integer $page
     * @param integer $pageSize
     * @return void
     * @author xsm
     * @since 2020-11-05
     */
    public function replayMe(int $uid, int $page, int $pageSize)
    {
        $where = [['to_uid', '=', $uid], ['delete_time', '=', '0']];
        $offset = ($page - 1) * $pageSize;
        $lists = $this->field([
            'id', 'cid', 'uid', 'content', 'create_time'
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


    public function getUnreadCommentNum(int $uid): string
    {
        if (!$uid) {
            return '';
        }
        $wxuser = Wxuser::where('uid', $uid)->field('last_read_comment_time')->find();
        $count = $this->where(
            [['create_time', '>', $wxuser['last_read_comment_time']], ['to_uid','=', $uid]]
        )->count();
        if ($count) {
            return (string)$count;
        }
        return "";
    }

    /**
     * 一级评论列表
     *
     * @param array $condition
     * @param integer $page
     * @param integer $pageSize
     * @return array
     * @author xsm
     * @since 2020-10-01
     */
    public function getList(array $condition, int $page, int $pageSize)
    {
        $where = [['pid', '=', 0], ['delete_time', '=', '0']];
        if (isset($condition['cid']) && !empty($condition['cid'])) {
            $where[] = ['cid', '=', intval($condition['cid'])];
        }
        $offset = ($page - 1) * $pageSize;
        $lists = $this->field([
            'id', 'uid', 'content', 'create_time'
        ])->where($where)->order('update_time', 'desc')->limit($offset, $pageSize)
            ->select()->toArray();
        $uids = $ids = [];
        foreach ($lists as &$list) {
            $list = $this->formatValue($list);
            $uids[] = $list['uid'];
            $ids[] = $list['id'];
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
        unset($list);
        $pids = join(',', $ids);
        $newlist = array_column($lists, null, 'id');
        $childList = $this->getChildList(['pid' => $pids], 1, 20);
        foreach ($childList as $child) {
            if (isset($newlist[$child['pid']])) {
                $newlist[$child['pid']]['child'][] = $child;
            }
        }
        return array_values($newlist);
    }

    /**
     * 二级评论（默认全部）
     *
     * @return void
     * @author xsm
     * @since 2020-10-01
     */
    public function getChildList(array $condition, int $page, int $pageSize): array
    {
        $where = [['delete_time', '=', '0']];
        if (isset($condition['pid']) && !empty($condition['pid'])) {
            $where[] = ['pid', 'IN', $condition['pid']];
        } else {
            return [];
        }
        $offset = ($page - 1) * $pageSize;
        $lists = $this->field([
            'id', 'pid', 'uid', 'content', 'create_time'
        ])->where($where)->order('update_time', 'asc')->limit($offset, $pageSize)
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
