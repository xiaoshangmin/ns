<?php

namespace app\api\controller\Misc;

use app\common\controller\Api;

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
        $this->success('ok',$list);
    }
}
