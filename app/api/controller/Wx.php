<?php

namespace app\api\controller;

use app\common\controller\Api;
use EasyWeChat\Factory;
use think\facade\Config;

/**
 * 会员接口.
 */
class Wx extends Api
{
    protected $noNeedLogin = ['wxlogin'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
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
            $this->success('ok',['token'=>$this->auth->getToken()]);
        }
        // TODO log

    }
}
