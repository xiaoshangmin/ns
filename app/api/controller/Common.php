<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Notice;

/**
 * 置顶配置数据
 */
class Common extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public $model = null;

    public function _initialize()
    {
        parent::_initialize();
    }


    public function config()
    {
        $help = (new Notice())->getList(['status' => 1, 'type' => 2], 1, 1);
        $notice = (new Notice())->getList(['status' => 1, 'type' => 3], 1, 1);
        $data = ['help' => []];
        if (isset($help[0])) {
            $data['help'] = $help[0];
        }
        if (isset($notice[0])) {
            $data['notice'] = $notice[0];
        }
        $this->success('ok',$data);
    }
}
