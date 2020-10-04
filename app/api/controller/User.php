<?php

namespace app\api\controller;

use app\common\model\Content;
use app\common\controller\Api;

/**
 * 会员接口.
 */
class User extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function mypost()
    {
        $page = $this->request->post('p/d') ?: 1;
        $pageSize = $this->request->post('ps/d') ?: 10;
        $status = $this->request->post('status');
        $list = (new Content())->getList(
            $this->auth->uid,
            ['uid' => $this->auth->uid, 'pay_status' => 1, 'status' => $status],
            ['update_time'=>'desc'],
            $page,
            $pageSize
        );
        $this->success('ok', ['list' => $list]);
    }
}
