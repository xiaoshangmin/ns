<?php

namespace app\admin\model;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Comment extends BaseModel
{

    

    
    use SoftDelete;
    // 表名
    protected $name = 'comment';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    // 追加属性
    protected $append = [
        'delete_time_text',
        'create_time_text',
        'update_time_text',
        'is_online_text',
    ];
    

    



    public function getDeleteTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delete_time']) ? $data['delete_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getCreateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['create_time']) ? $data['create_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getUpdateTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['update_time']) ? $data['update_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setDeleteTimeAttr($value)
    {
        return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setCreateTimeAttr($value)
    {
        return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setUpdateTimeAttr($value)
    {
        return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function getIsOnlineList()
    {
        return ['0' => __('Is_online 0'), '1' => __('Is_online 1')];
    }

}
