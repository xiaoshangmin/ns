<?php

namespace app\common\model;

/**
 * 会员模型.
 */
class Wxuser extends BaseModel
{
    protected $pk = 'uid';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 追加属性
    protected $append = [];

    public function getUserByUids(array $uids): array
    {
        if (empty($uids)) {
            return [];
        }
        $uids = join(',', $uids);
        $list = $this->field([
            'uid', 'nickname', 'headimgurl'
        ])->where('uid', 'in', $uids)->select()->toArray();
        return $list;
    }
}
