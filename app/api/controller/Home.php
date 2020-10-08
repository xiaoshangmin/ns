<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Ads;
use app\common\model\Navigation;
use app\common\model\Columns;

/**
 * 首页接口.
 */
class Home extends Api
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
    public function index()
    {
        // $params = $this->request->post();
        $data['banner'] = (new Ads())->getBannerList();
        $data['navigation'] = (new Navigation())->getNavList();
        $data['notice'] = [
            [
                'id' => 1, 'title' => '发布须知！',
            ],
            [
                'id' => 2, 'title' => '关于我们',
            ]
        ];
        //一级栏目
        $columns = new Columns();
        $pcloumn = $columns->getList(['status' => 1, 'pid' => 0]);
        $data['pcolumn'] = $pcloumn;
        //多级栏目
        $column = $pcloumn;
        // foreach ($pcloumn as &$val) {
        //     $child = $columns->getList(['status' => 1, 'pid' => $val['id']]);
        //     $column = array_merge($column, $child);
        // }
        $column = [
            'name' => '全部',
            'child' => $column,
        ];
        $data['columns'] = $column;
        $this->success('ok', $data);
    }

    /**
     * 专栏列表
     *
     * @return void
     * @author xsm
     * @since 2020-09-19
     */
    public function columnList()
    {
        //一级栏目
        $columns = new Columns();
        $pcloumn = $columns->getList(['status' => 1, 'pid' => 0]);
        $child = [];
        //多级栏目 
        foreach ($pcloumn as &$val) {
            $child[$val['id']] = $columns->getList(['status' => 1, 'pid' => $val['id']]);
        }
        $this->success('ok', ['pcloumn' => $pcloumn, 'child' => $child]);
    }
}