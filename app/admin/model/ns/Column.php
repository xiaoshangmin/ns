<?php

namespace app\admin\model\ns;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Column extends BaseModel
{




    use SoftDelete;
    // 表名
    protected $name = 'column';

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
        'delete_time_text',
        'status_text'
    ];



    public function getStatusList()
    {
        return ['0' => __('Status 0'), '1' => __('Status 1')];
    }

    public function getPcloumnList()
    {
        $level1 = $this->field('id,name')->where('pid', 0)->select()->toArray();
        $newList = [];
        foreach($level1 as $val){
            $newList[] = $val;
            $level2 = $this->field('id,name')->where('pid', $val['id'])->select()->toArray();
            if($level2){
                foreach($level2 as &$v){
                    $v['name'] = '└'.$v['name'];
                }
                $newList = array_merge($newList,$level2);
            }
        }
        return $newList;

    }

    public function getTreeList()
    {
        $total = $this->order('id', 'desc')->select()->toArray();
        $treeList = [];
        $pList = $this->getChild(0, $total);

        foreach ($pList as $data) {
            $child = $this->getChild($data['id'], $total);
            $data['haschild'] = 0;
            if ($child) {
                $data['haschild'] = 1;
            }
            if ($data['id']) {
                $treeList[] = $data;
            }
            if ($child) {
                $newchild = [];
                foreach($child as $v){
                    $level3 = $this->getChild($v['id'], $total);
                    $v['haschild'] = 0;
                    if($level3){
                        $v['haschild'] = 1;
                    }
                    if ($v['id']) {
                        $newchild[] = $v;
                    }
                    if($level3){
                        $newchild = array_merge($newchild, $level3);
                    }
                }

                $treeList = array_merge($treeList, $newchild);
            }
        }
        return $treeList;
    }

    public function getChild(int $myid, array $data): array
    {
        $newarr = [];
        foreach ($data as $value) {
            if (!isset($value['id'])) {
                continue;
            }
            if ($value['pid'] == $myid) {
                if ($myid > 0) {
                    $value['name'] = "└" . $value['name'];
                }
                $newarr[$value['id']] = $value;
            }
        }

        return $newarr;
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


    public function getDeleteTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['delete_time']) ? $data['delete_time'] : '');
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

    protected function setDeleteTimeAttr($value)
    {
        return $value === '' ? null : ($value && !is_numeric($value) ? strtotime($value) : $value);
    }

    protected function setPriceAttr($value, $data)
    {
        if ($data['pid'] > 0) {
            return 0;
        }
        return $value;
    }

    protected function setRefreshPriceAttr($value, $data)
    {
        if ($data['pid'] > 0) {
            return 0;
        }
        return $value;
    }
}
