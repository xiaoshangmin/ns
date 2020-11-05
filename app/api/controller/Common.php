<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\{Ads, Navigation, Columns, Notice, Comment};

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
        $data['popupAd'] = (new Ads())->getPopupList();
        $data['banner'] = (new Ads())->getBannerList();
        $data['navigation'] = (new Navigation())->getNavList();
        $data['notice'] = (new Notice())->getList(['status' => 1, 'type' => 1], 1, 10);
        //一级栏目
        $columns = new Columns();
        $pcloumn = $columns->getList(['status' => 1, 'pid' => 0]);
        $data['pcolumn'] = $pcloumn;
        //多级栏目
        $column = $pcloumn;
        $first = ['id' => 0, 'name' => '全部'];
        array_unshift($column, $first);
        $column = [
            'name' => '全部',
            'child' => $column,
        ];
        $data['columns'] = $column;
        $this->success('ok', $data);
    }
}
