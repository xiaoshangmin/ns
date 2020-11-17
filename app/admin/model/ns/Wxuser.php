<?php

namespace app\admin\model\ns;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Wxuser extends BaseModel
{

    
    use SoftDelete;
    protected $pk = 'uid';

    // 表名
    protected $name = 'wxuser';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    // 追加属性
    protected $append = [
        'sex_text',
        'create_time_text',
        'update_time_text',
        'status_text'
    ];
    

    
    public function getSexList()
    {
        return ['1' => __('Sex 1'), '2' => __('Sex 2')];
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }


    public function getSexTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['sex']) ? $data['sex'] : '');
        $list = $this->getSexList();
        return isset($list[$value]) ? $list[$value] : '';
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


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setCreateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setUpdateTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function getDeleteTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delete_time']) ? $data['delete_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }
    
    protected function setDeleteTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

}
