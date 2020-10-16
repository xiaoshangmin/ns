<?php

namespace app\common\model;

class Notice extends BaseModel
{


    // 表名
    protected $name = 'notice';


    public function getById(int $id)
    {
        $where = [
            'id' => $id,
            'status' => 1
        ];
        return $this->where($where)->find();
    }

    public function getList(array $condition,int $page,int $pageSize)
    {
        $where = [];
        
        if (isset($condition['status'])) {
            $where[] = ['status', '=', intval($condition['status'])];
        }
        if (isset($condition['type'])) {
            $where[] = ['type', '=', intval($condition['type'])];
        }
        $offset = ($page - 1) * $pageSize;
        $list = $this->field('id,title')
            ->where($where)
            ->order('id', 'desc')
            ->limit($offset,$pageSize)
            ->select()->toArray();
        return $list;
    }
}
