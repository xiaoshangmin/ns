<?php

namespace app\api\controller;

use app\common\controller\Api;
use EasyWeChat\Factory;
use think\facade\Config;
use app\common\model\Content;
use app\common\model\ColumnContent;
use think\facade\Log;
use app\common\model\Orders;
use App\Controller\Pccontent\Column;

/**
 * 会员接口.
 */
class Wx extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 微信支付回调
     *
     * @return void
     * @author xsm
     * @since 2020-09-20
     */
    public function paynotify()
    {
        //回调判断
        $config = Config::get('api.miniprogram.ns');
        $app = Factory::payment($config);
        $response = $app->handlePaidNotify(function ($message, $fail) {
            $log = 'paynotify:' . json_encode($message, JSON_UNESCAPED_UNICODE);
            Log::record($log);
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Orders::where('order_sn', $message['out_trade_no'])->find();

            if (!$order || $order->pay_time) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////
            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if ($message['result_code'] === 'SUCCESS') {
                    $order->pay_time = time(); // 更新支付时间为当前时间
                    $order->status = Orders::PAY_SUCCESS;
                    (new Content())->changePayStatus($order->cid,Orders::PAY_SUCCESS);
                    // 用户支付失败
                } elseif ($message['result_code'] === 'FAIL') {
                    $order->status = Orders::PAY_FAIL;
                    (new Content())->changePayStatus($order->cid,Orders::PAY_FAIL);
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save(); // 保存订单

            return true; // 返回处理完成
            // 或者错误消息
            // $fail('Order not exists.');
        });

        return $response;
    }

    public function wxlogin()
    {
        $code = $this->request->post('code');
        $config = Config::get('api.miniprogram.ns');
        $app = Factory::miniProgram($config);
        $wxuser = $app->auth->session($code);
        if (isset($wxuser['openid'])) {
            $user = \app\common\model\Wxuser::where('openid', $wxuser['openid'])->find();
            if ($user) {
                if ($user->status != 1) {
                    $this->error(__('Account is locked'));
                }
                //如果已经有账号则直接登录
                $this->auth->wxdirect($user->uid);
            } else {
                $this->auth->wxregister($wxuser['openid'], $wxuser['session_key']);
            }
            $this->success('ok', ['token' => $this->auth->getToken()]);
        }
        // TODO log

    }
}
