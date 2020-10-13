<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Notice as NoticeModel;

/**
 * 首页接口.
 */
class Notice extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

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
    public function detail()
    {
        $id = $this->request->get('id/d');
        $data = (new NoticeModel())->getById($id);;
        $this->success('ok', $data);
    }
}
