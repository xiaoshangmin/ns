<?php

namespace app\common\model;

/**
 * 订单模型.
 */
class Orders extends BaseModel
{
    // 表名
    protected $name = 'orders';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    // 追加属性
    protected $append = [];

    const PAY_SUCCESS = 1;
    const PAY_FAIL = 2;
    const NO_PAY = 0;

    public static function onBeforeInsert($order)
    {
        $order->order_sn = date("YmdHis") . substr(microtime(), 2, 4);
        $order->order_status = 0;
    }

    public function add(array $data)
    {
        $insert['uid'] = intval($data['uid']);
        $insert['cid'] = intval($data['cid']);
        $insert['order_amount'] = floatval($data['orderAmount']);
        $insert['top_id'] = intval($data['top_id']);
        $this->data($insert, true)->save();
        return $this;
    }
}
