<?php

namespace app\common\model;


/**
 * 内容模型.
 */
class LikeLog extends BaseModel
{
    // 表名
    protected $name = 'like_log';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    const CONTENT_LIKE_TYPE = 1;
    const COMMENT_LIKE_TYPE = 2;

    public function removeLog(int $uid, int $targetId, int $type)
    {
        $this->whereRaw("uid={$uid} AND target_id={$targetId} AND type={$type}")->delete();
    }

    public function addLog(int $uid, int $targetId, int $type)
    {
        $insert = [
            'uid' => $uid,
            'target_id' => $targetId,
            'type' => $type,
        ];
        $this->data($insert, true)->save();
    }

    public function isLike(int $uid, int $targetId, int $type)
    {
        $hasLikeLog = $this->whereRaw("uid={$uid} AND target_id={$targetId} AND type={$type}")->find();
        if (empty($hasLikeLog)) {
            return false;
        }
        return true;
    }

    /**
     * 批量获取用户点赞的内容
     *
     * @param integer $uid
     * @param array $targetIds
     * @param integer $type
     * @return array
     * @author xsm
     * @since 2020-07-23
     */
    public function getUserLikeContentByTargetId(int $uid, array $targetIds): array
    {
        if (empty($targetIds)) {
            return [];
        }
        $targetIds = join(',', $targetIds);
        $where =  "uid = {$uid} AND target_id IN({$targetIds}) AND type=" . self::CONTENT_LIKE_TYPE;
        $data = $this->field('`id`,`uid`,`target_id`,`type`,`create_time`')
            ->whereRaw($where)->select()->toArray();
        if ($data) {
            return $data;
        }
        return [];
    }
}
