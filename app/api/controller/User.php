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
            ['uid' => $this->auth->uid, 'pay_status' => 1, 'status' => $status],
            $page,
            $pageSize
        );
        $this->success('ok', ['list' => $list]);
    }
}
