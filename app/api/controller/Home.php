<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\{Comment, Wxuser};

/**
 * 首页接口.
 */
class Home extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 未读数
     *
     * @return void
     * @author xsm
     * @since 2020-09-16
     */
    public function index()
    {
        $uid = $this->auth->uid ?: 0;
        $user = Wxuser::where('uid', $uid)->find();
        $unread = (new Comment())->getUnreadCommentNum($uid);
        $data['common'] = ['unread' => $unread];
        $data['need_auth'] = false; //是否需要再次授权
        if ($user && $user->delete_time > 0) {
            $data['need_auth'] = true;
        }
        $this->success('ok', $data);
    }
}
