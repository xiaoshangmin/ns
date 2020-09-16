<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Ads;
use app\common\model\Columns;
use think\facade\Config;

/**
 * 首页接口.
 */
class Home extends Api
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = [''];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 首页 banner,栏目，头条必读
     *
     * @return void
     * @author xsm
     * @since 2020-09-16
     */
    public function index()
    {
        $params = $this->request->post();
        $data['banner'] = (new Ads())->getBannerList();
        //一级栏目
        $pcloumn = (new Columns())->getList(['status' => 1, 'pid' => 0]);
        $data['pcolumn'] = $pcloumn;
        $data['notice'] = [];
        //多级栏目

        $data['column'] = [];
        $this->success('ok', $data);
    }
}
