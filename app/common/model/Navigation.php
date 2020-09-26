<?php

namespace app\common\model;
  
class Navigation extends BaseModel
{

    

    

    // 表名
    protected $name = 'navigation';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    public function getNavList()
    {
        $condition = ['status' => 1];
        $list = $this->getList($condition);
        return $list;
    }

    public function getList(array $condition)
    {
        $where = [];
        if (isset($condition['status'])) {
            $where[] = ['status', '=', intval($condition['status'])];
        }
        $list = $this->field('id,title,picture,link_type,link_info')
            ->where($where)->order('sort', 'desc')
            ->select()->toArray();
        return $list;
    }

}
