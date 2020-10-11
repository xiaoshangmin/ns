<?php

namespace app\api\controller;

use app\common\model\{Content, Wxuser};
use app\common\controller\Api;
use think\facade\Config;
use EasyWeChat\Factory;

/**
 * 会员接口.
 */
class User extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = [];

    public function mypost()
    {
        $page = $this->request->post('p/d') ?: 1;
        $pageSize = $this->request->post('ps/d') ?: 10;
        $status = $this->request->post('status');
        $list = (new Content())->getList(
            $this->auth->uid,
            ['uid' => $this->auth->uid, 'pay_status' => 1, 'status' => $status],
            ['update_time' => 'desc'],
            $page,
            $pageSize
        );
        $this->success('ok', ['list' => $list]);
    }

    public function decrypt()
    {
        $iv = $this->request->post('iv');
        $encryptedData = $this->request->post('encryptedData');
        $user = (new Wxuser())->where('uid', $this->auth->uid)->find();
        if (isset($user['session_key'])) {
            $config = Config::get('api.miniprogram.ns');
            $app = Factory::miniProgram($config);
            $decryptedData = $app->encryptor->decryptData($user['session_key'], $iv, $encryptedData);

            $update = [
                'unionid' => $decryptedData['unionId'],
                'nickname' => $decryptedData['nickName'],
                'sex' => $decryptedData['gender'],
                'language' => $decryptedData['language'],
                'country' => $decryptedData['country'],
                'province' => $decryptedData['province'],
                'city' => $decryptedData['city'],
                'headimgurl' => $decryptedData['avatarUrl'],
            ];
            Wxuser::update($update, ['uid' => $user['uid']]);
        }
        $this->success();
    }
}
