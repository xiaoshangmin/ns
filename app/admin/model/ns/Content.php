<?php

namespace app\admin\model\ns;

use app\common\model\BaseModel;


class Content extends BaseModel
{

    

    

    // 表名
    protected $name = 'content';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'create_time_text',
        'update_time_text',
        'expiry_time_text',
        'status_text',
        'top_text',
        'pay_status_text'
    ];
    

    public function getPicturesAttr($value, $data)
    {
        if($value){
            $config = get_addon_config('cloudstore');
            $qiniuDomain = $config['domain'];
            $pictures = json_decode($value,true);
            $pics = [];
            foreach($pictures as $pic){
                $pics[] = $qiniuDomain . '/' . $pic['key'];
            }
            return join(',',$pics);
        }
        return '';
    }
    
    public function setPicturesAttr($value)
    {
        $keys = [];
        if($value){
            $pics = explode(',',$value);
            foreach($pics as $pic){
                $basename = pathinfo($pic,PATHINFO_BASENAME);
                $keys[] = ['key'=>$basename];
            }
        }
        return json_encode($keys,JSON_UNESCAPED_UNICODE);
    }

    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1'), '2' => __('Status 2')];
    }

    public function getTopList()
    {
        return ['0' => __('Top 0'), '1' => __('Top 1')];
    }

    public function getPayStatusList()
    {
        return ['0' => __('Pay_status 0'), '1' => __('Pay_status 1'), '2' => __('Pay_status 2')];
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


    public function getExpiryTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['expiry_time']) ? $data['expiry_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getTopTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['top']) ? $data['top'] : '');
        $list = $this->getTopList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPayStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_status']) ? $data['pay_status'] : '');
        $list = $this->getPayStatusList();
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

    protected function setExpiryTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }


}
