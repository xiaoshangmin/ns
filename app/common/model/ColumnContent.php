<?php

namespace app\common\model;

/**
 * 栏目关联内容模型.
 */
class ColumnContent extends BaseModel
{
    // 表名
    protected $name = 'column_content';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 追加属性
    protected $append = [];


    public function getHomeList(array $condition, int $page, int $pageSize): array
    {
        $topList = $this->getTopList($condition, $page, $pageSize);
        $diff = $pageSize - count($topList);
        $list = [];
        if ($diff > 0) {
            $where = [
                ['pay_status', '=', 1],
                ['expiry_time', '<', time()],
                ['status', '=', 1],
                ['is_online', '=', 1],
            ];
            if (isset($condition['column_id']) && !empty($condition['column_id'])) {
                $where[] = ["column_id", '=', $condition['column_id']];
            }
            $list = $this->getRelateList($where, $page, $diff);
        }
        $list = array_merge($topList, $list);
        return $list;
    }

    public function getTopList(array $condition, int $page, int $pageSize): array
    {
        $where = [
            ['pay_status', '=', 1],
            ['top', '=', 1],
            ['expiry_time', '>', time()],
            ['status', '=', 1],
            ['is_online', '=', 1],
        ];
        if (isset($condition['column_id']) && !empty($condition['column_id'])) {
            $where[] = ["column_id", '=', $condition['column_id']];
        }
        $list = $this->getRelateList($where, $page, $pageSize);
        return $list;
    }

    /**
     * 获取关联的内容ID
     *
     * @param array $condition
     * @param integer $page
     * @param integer $pageSize
     * @return array
     * @author xsm
     * @since 2020-09-20
     */
    public function getRelateList(array $condition, int $page, int $pageSize): array
    {
        $offset = ($page - 1) * $pageSize;
        $list = $this->field('cid')->where($condition)
            ->order('update_time', 'desc')->limit($offset, $pageSize)
            ->select()->toArray();
        return $list;
    }

    /**
     * 添加关联内容
     *
     * @param integer $columnId
     * @param integer $cid
     * @param array $params
     * @return void
     * @author xsm
     * @since 2020-09-20
     */
    public function addRelateContent(int $columnId, int $cid, array $params)
    {
        $data = [
            'column_id' => $columnId,
            'cid' => $cid,
        ];
        $data = array_merge($data, $params);
        $this->save($data);
        return $this;
    }

    /**
     * 通过内容ID获取关联的栏目
     *
     * @param array $condition
     * @param integer $page
     * @param integer $pageSize
     * @return array
     * @author xsm
     * @since 2020-09-20
     */
    public function getRelateColumnListByCids(array $cids): array
    {
        if (empty($cids)) {
            return [];
        }
        $list = $this->field('cid,column_id')->where('cid', 'in', join(',', $cids))
            ->select()->toArray();
        if (empty($list)) {
            return [];
        }
        $columnIds = array_unique(array_column($list, 'column_id'));
        $columnInfo = (new Columns())->getList(['ids' => $columnIds]);
        $columnInfo = array_column($columnInfo, null, 'id');
        $newlist = [];
        foreach ($list as $c) {
            $newlist[$c['cid']][] = $columnInfo[$c['column_id']];
        }
        return $newlist;
    }
}
