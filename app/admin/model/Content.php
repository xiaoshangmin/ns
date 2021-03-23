<?php

namespace app\admin\model;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use Geohash;

class Content extends BaseModel
{




    use SoftDelete;
    // 表名
    protected $name = 'content';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    // protected $json = ['extra'];

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
        'addr_column_id',
    ];

    public static function onBeforeInsert($model)
    {
        $model->geohash = (new Geohash())->encode($model->lat, $model->lng);
    }

    /**
     * 获取栏目第一个值
     *
     * @param [type] $value
     * @param [type] $data
     * @return void
     * @author xsm
     * @since 2020-12-26
     */
    public function getPcolumnIdAttr($value, $data)
    {
        if ($data['column_ids']) {
            $columnIds = explode(',', $data['column_ids']);
            return $columnIds[0] ?? 0;
        }
        return 0;
    }

    /**
     * 获取栏目第二值
     *
     * @param [type] $value
     * @param [type] $data
     * @return void
     * @author xsm
     * @since 2020-12-26
     */
    public function getColumnIdAttr($value, $data)
    {
        if ($data['column_ids']) {
            $columnIds = explode(',', $data['column_ids']);
            return $columnIds[1] ?? 0;
        }
        return 0;
    }

    /**
     * 获取栏目第三个值
     *
     * @param [type] $value
     * @param [type] $data
     * @return void
     * @author xsm
     * @since 2020-12-26
     */
    public function getAddrColumnIdAttr($value, $data)
    {
        if ($data['column_ids']) {
            $columnIds = explode(',', $data['column_ids']);
            return $columnIds[2] ?? 0;
        }
        return 0;
    }

    /**
     * 获取图片url
     *
     * @param [type] $value
     * @param [type] $data
     * @return void
     * @author xsm
     * @since 2020-12-26
     */
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

    /**
     * 格式化图片字段json
     *
     * @param [type] $value
     * @return void
     * @author xsm
     * @since 2020-12-26
     */
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

    /**
     * 格式化管理员可见信息json
     *
     * @param [type] $value
     * @return void
     * @author xsm
     * @since 2020-12-26
     */
    protected function setExtraAttr($value)
    {
        if (empty($value)) {
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }
        return $value;
    }

    protected function setPcolumnIdAttr($value, $data)
    {
        $columnIds = [];
        $columnIds[] = $data['pcolumn_id'] ?? 0;
        $columnIds[] = $data['column_id'] ?? 0;
        $columnIds[] = $data['addr_column_id'] ?? 0;
        $columnIds = array_filter($columnIds);
        $this->set('column_ids', join(',', $columnIds));
    }

    /**
     * 动态设置栏目ids值
     *
     * @param [type] $content
     * @return void
     * @author xsm
     * @since 2020-12-26
     */
    public static function onBeforeWrite($content)
    {
        $columnIds[] = $content->pcolumn_id ?: 0;
        $columnIds[] = $content->column_id ?: 0;
        $columnIds[] = $content->addr_column_id ?: 0;
        $columnIds = array_filter($columnIds);
        $columnIds =  join(',', $columnIds);
        $columnIds = rtrim($columnIds, ',');
        $content->column_ids = $columnIds;
    }

    public static function onAfterWrite($content)
    {
        // 真实删除
        ColumnContent::destroy(function ($query) use ($content) {
            $query->where('cid', $content->id);
        }, true);
        $columnIds = explode(',', $content->column_ids);
        //栏目关联内容
        foreach ($columnIds as $columnId) {
            $insert = [
                'column_id' => $columnId,
                'cid' => $content->id,
                'top' => $content->top ?? 0,
                'expiry_time' => $content->expiry_time ?? 0,
                'status' => $content->status ?? 0,
                'pay_status' => $content->pay_status ?? 0,
                'is_online' => $content->is_online ?? 0,
            ];
            (new ColumnContent)->save($insert);
        }
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

    protected function setExpiryTimeAttr($value, $data)
    {
        if ($data['top']) {
            return $value === '' ? 0 : ($value && !is_numeric($value) ? strtotime($value) : $value);
        }
        return 0;
    }

    public function getDeleteTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delete_time']) ? $data['delete_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    public function wxuser()
    {
        return $this->belongsTo('Wxuser', 'uid');
    }
}
