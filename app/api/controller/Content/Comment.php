<?php

namespace app\api\controller\Content;

use app\common\controller\Api;
use app\common\model\Comment as CommentModel;
/**
 * 首页接口.
 */
class Comment extends Api
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = [''];
    public $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new CommentModel();
    }

    public function listPrimary()
    {
        $params = $this->request->post();
        $page = $this->request->post('p/d') ?: 1;
        $pageSize = $this->request->post('ps/d') ?: 10;
        $list = $this->model->getList($params, $page, $pageSize);
        $this->success('ok', ['list' => $list]);
    }

    public function detail()
    {
        $cid = $this->request->get('cid/d');
        $detail = $this->model->getById($cid);
        $this->success('ok', $detail);
    }

    public function submit()
    {
        $params = $this->request->post();
        if ($params) {
            try {
                validate(\app\api\validate\content\Feed::class)->check($params);
            } catch (ValidateException $e) {
                $this->error($e->getMessage());
            }
            $params['uid'] = $this->auth->uid;
            $result = $this->model->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }
            $params['cid'] = $this->model->id;
            //新增订单
            $order = new Orders();
            $ret = $order->add($params);
            //生成预支付信息
            $result = $this->getPrepayInfo([
                'out_trade_no' => $ret->order_sn,
                'openid' => $this->auth->openid,
                'total_fee' => $ret->order_amount,
            ]);
            $this->success('ok', $result);
        }
        $this->error();
    }

    public function getPrepayInfo(array $data)
    {
        $config = Config::get('api.miniprogram.ns');
        $app = Factory::payment($config);
        $result = $app->order->unify([
            'body' => '南沙小程序-发布内容',
            'out_trade_no' => $data['out_trade_no'],
            'total_fee' => $data['total_fee'],
            'trade_type' => 'JSAPI',
            'openid' => $data['openid'],
        ]);
        return $result;
    }
}
