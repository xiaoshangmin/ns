<?php

namespace app\common\model;

/**
 * 会员余额日志模型.
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
    // 追加属性
    protected $append = [];
    // protected $insert = [
    //     'expiry_time' => 0
    // ];

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

    // public function setExpiryTimeAttr($value, $data)
    // {
    //     return time();
    // }

    public function add(int $uid, array $data)
    {
    }

    public function getList(string $column = '*'): array
    {
        if ('*' == $column) {
            $column = 'id,price,type';
        }
        $list = $this->field($column)->order('sort', 'desc')->select()->toArray();
        foreach ($list as &$item) {
        }
        return $list;
    }
}
