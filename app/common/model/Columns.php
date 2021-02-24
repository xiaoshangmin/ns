<?php

namespace app\common\model;

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
 
    protected $deleteTime = 'delete_time'; 

    public function getList(array $condition):array
    {
        $where[] = ['delete_time','=','0'];
        if (isset($condition['status'])) {
            $where[] = ['status', '=', intval($condition['status'])];
        }
        if (isset($condition['pid'])) {
            $where[] = ['pid', '=', intval($condition['pid'])];
        }
        if (isset($condition['ids']) && !empty($condition['ids'])) {
            $where[] = ['id', 'IN', join(',', $condition['ids'])];
        }
        $list = $this->field('id,icon,price,template,refresh_price,name,level,create_time')
            ->where($where)->order('sort', 'desc')->select()->toArray();
        return $list;
    }
}
