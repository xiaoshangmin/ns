<?php

namespace app\common\model;
use think\model\concern\SoftDelete;

/**
 * 栏目模型.
 */
class Columns extends BaseModel
{
    // 表名
    protected $name = 'column';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 追加属性
    protected $append = [];

    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
}
