<?php

namespace app\admin\model\ns;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Content extends BaseModel
{




    use SoftDelete;
    // 表名
    protected $name = 'content';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    // 追加属性
    protected $append = [
        'create_time_text',
        'update_time_text',
        'expiry_time_text',
        'status_text',
        'top_text',
        'pay_status_text',
        'is_online_text',
        'pcolumn_id',
        'column_id',
    ];

    public function getPcolumnIdAttr($value, $data)
    {
        if ($data['column_ids']) {
            $columnIds = explode(',', $data['column_ids']);
            return $columnIds[0] ?? 0;
        }
        return 0;
    }

    public function getColumnIdAttr($value, $data)
    {
        if ($data['column_ids']) {
            $columnIds = explode(',', $data['column_ids']);
            return $columnIds[1] ?? 0;
        }
        return 0;
    }

    public function getPicturesAttr($value, $data)
    {
        if ($value) {
            $config = get_addon_config('cloudstore');
            $qiniuDomain = $config['domain'];
            $pictures = json_decode($value, true);
            $pics = [];
            foreach ($pictures as $pic) {
                $pics[] = $qiniuDomain . '/' . $pic['key'];
            }
            return join(',', $pics);
        }
        return '';
    }

    protected function setPicturesAttr($value)
    {
        $keys = [];
        if ($value) {
            $pics = explode(',', $value);
            foreach ($pics as $pic) {
                $basename = pathinfo($pic, PATHINFO_BASENAME);
                $keys[] = ['key' => $basename];
            }
        }
        return json_encode($keys, JSON_UNESCAPED_UNICODE);
    }

    protected function setColumnIdsAttr($value,$data)
    {
        $columnIds = [];
        $columnIds[] = $data['pcolumn_id'] ?? 0;
        $columnIds[] = $data['column_id'] ?? 0;
        return join(',',$columnIds);
    }

    public static function onBeforeWrite($content)
    {
        $columnIds[] = $content->pcolumn_id ?: 0;
        $columnIds[] = $content->column_id ?: 0;
        $columnIds = array_filter($columnIds);
        $columnIds =  join(',',$columnIds);
        $columnIds = rtrim($columnIds,',');
        $content->column_ids = $columnIds;
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

    public function getIsOnlineList()
    {
        return ['0' => __('Is_online 0'), '1' => __('Is_online 1')];
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

    public function getIsOnlineTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['is_online']) ? $data['is_online'] : '');
        $list = $this->getIsOnlineList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setUpdateTimeAttr($value)
    {
        return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setExpiryTimeAttr($value)
    {
        return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    public function getDeleteTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delete_time']) ? $data['delete_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }
    
    protected function setDeleteTimeAttr($value)
    {
        return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }
}
