<?php

namespace app\common\model;

/**
 * 会员余额日志模型.
 */
class TopConfig extends BaseModel
{
    // 表名
    protected $name = 'top_config';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = '';
    // 追加属性
    protected $append = [];

    const TYPE = [
        1 => '置顶一天',
        2 => '置顶一周',
        3 => '置顶一个月',
    ];

    public function getList(string $column = '*'): array
    {
        if ('*' == $column) {
            $column = 'id,price,type';
        }
        $list = $this->field($column)->order('sort', 'desc')->select()->toArray();
        foreach ($list as &$item) {
            $item['name'] = self::TYPE[$item['type']] . "(收费{$item['price']}元)";
        }
        return $list;
    }
}
