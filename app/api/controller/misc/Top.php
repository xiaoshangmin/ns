<?php

namespace app\api\controller\Misc;

use app\common\controller\Api;
use app\common\model\Wxuser;

/**
 * 置顶配置数据
 */
class Top extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    public $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\TopConfig();
    }


    public function config()
    {
        $list = $this->model->getList();
        $user = (new Wxuser())->field('mobile')->where('uid', $this->auth->uid)->find();
        $this->success('ok',['top'=>$list,'user'=>$user]);
    }
}
