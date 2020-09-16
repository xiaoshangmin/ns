<?php

namespace app\common\model;

/**
 * 栏目模型.
 */
class Ads extends BaseModel
{
    // 表名
    protected $name = 'ads';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 追加属性
    protected $append = [];

    public function getBannerList()
    {
        $condition = ['status' => 1, 'type' => 1];
        $list = $this->getList($condition);
        return $list;
    }

    public function getList(array $condition)
    {
        $where = [];
        if (isset($condition['status'])) {
            $where[] = ['status', '=', intval($condition['status'])];
        }
        if (isset($condition['type'])) {
            $where[] = ['type', '=', intval($condition['type'])];
        }
        $list = $this->field('id,type,title,picture,link_type,link_info')
            ->where($where)->order('sort', 'desc')
            ->select()->toArray();
        return $list;
    }
}
