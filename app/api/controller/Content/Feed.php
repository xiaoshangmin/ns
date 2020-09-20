<?php

namespace app\api\controller\Content;

use app\common\controller\Api;
use think\exception\ValidateException;
use app\common\model\Content;
use app\common\model\Orders;
use app\common\model\Columns;
use app\common\model\ColumnContent;
use app\common\model\TopConfig;
use think\facade\Config;
use think\facade\Log;
use EasyWeChat\Factory;

/**
 * 首页接口.
 */
class Feed extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = [];
    public $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new Content();
    }

    public function lists()
    {
        $page = $this->request->post('p/d') ?: 1;
        $pageSize = $this->request->post('ps/d') ?: 10;
        $columnId = $this->request->post('column_id/d') ?: 0;
        $list = $this->model->getHomeList(['column_id' => $columnId], $page, $pageSize);
        $this->success('ok', ['list' => $list]);
    }

    public function detail()
    {
        //ALTER TABLE ns_content ADD FULLTEXT INDEX ft_index (content) WITH PARSER ngram;
        $cid = $this->request->get('cid/d');
        $detail = $this->model->getById($cid);
        $this->success('ok', $detail);
    }

    public function submit()
    {
        $params = $this->request->post();
        $log = 'submit:' . json_encode($params, JSON_UNESCAPED_UNICODE);
        Log::record($log);
        if ($params) {
            try {
                validate(\app\api\validate\content\Feed::class)->check($params);
            } catch (ValidateException $e) {
                $this->error($e->getMessage());
            }
            $params['uid'] = $this->auth->uid;
            //订单总金额
            $orderAmount = 0;
            //获取栏目 
            $cloumns = explode(',', $params['column_ids']);
            foreach ($cloumns as $cloumnId) {
                $columnInfo = Columns::find($cloumnId);
                if (empty($columnInfo)) {
                    $this->error('栏目不存在或已下架');
                }
                $orderAmount = bcadd($orderAmount, $columnInfo['price']);
            }
            unset($cloumnId);
            //保存内容主体信息
            $result = $this->model->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }
            $params['cid'] = $this->model->id;
            //栏目关联内容
            foreach ($cloumns as $cloumnId) {
                (new ColumnContent())->addRelateContent($cloumnId, $params['cid'], [
                    'top' => $this->model->top,
                    'expiry_time' => $this->model->expiry_time,
                ]);
            }
            //获取置顶类型
            if (isset($params['top_id']) && !empty($params['top_id'])) {
                $topInfo = TopConfig::find($params['top_id']);
                $orderAmount = bcadd($orderAmount, $topInfo['price']);
            }
            $params['orderAmount'] = $orderAmount;
            //新增订单
            if ($orderAmount) {
                $order = new Orders();
                $ret = $order->add($params);
                //生成预支付信息
                $result = $this->getPrepayInfo([
                    'out_trade_no' => $ret->order_sn,
                    'openid' => $this->auth->openid,
                    'total_fee' => $ret->order_amount,
                ]);
                $log = 'getPrepayInfo:' . json_encode($result, JSON_UNESCAPED_UNICODE);
                Log::record($log);
                if ($result) {
                    $this->success('ok', $result, 1000);
                }
            } else {
                //免费发布
                $this->model->changePayStatus($params['cid'], Orders::PAY_SUCCESS);
            }
            $this->success('ok');
        }
        $this->error();
    }

    /**
     * 获取小程序支付信息
     *
     * @param array $data
     * @return array
     * @author xsm
     * @since 2020-09-19
     */
    public function getPrepayInfo(array $data): array
    {
        $config = Config::get('api.miniprogram.ns');
        $app = Factory::payment($config);
        $result = $app->order->unify([
            'body' => '南沙小程序-发布内容',
            'out_trade_no' => $data['out_trade_no'],
            'total_fee' => $data['total_fee'] * 100,
            'trade_type' => 'JSAPI',
            'openid' => $data['openid'],
        ]);
        if (
            isset($result['return_code']) && 'SUCCESS' == $result['return_code']
            && isset($result['result_code']) && 'SUCCESS' == $result['result_code']
        ) {
            $jssdk = $app->jssdk;
            $config = $jssdk->bridgeConfig($result['prepay_id'], false); // 返回数组
            return $config;
        }
        return [];
    }
}
