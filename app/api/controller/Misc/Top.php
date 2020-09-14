<?php

namespace app\api\controller\Misc;

use app\common\controller\Api;

/**
 * 会员接口.
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
        // print_r($this->model);
        $this->success('ok',$list);
    }
}
