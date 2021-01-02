<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\{Ads, Navigation, Columns, Notice, Comment};

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
        $uid = $this->auth->uid ? :0;
        $unread = (new Comment())->getUnreadCommentNum($uid);
        $data['common'] = ['unread' => $unread];
        $this->success('ok', $data);
    }
}
