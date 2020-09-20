<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Ads;
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
        //一级栏目
        $columns = new Columns();
        $pcloumn = $columns->getList(['status' => 1, 'pid' => 0]);
        $data['pcolumn'] = $pcloumn;
        $data['notice'] = [
            [
                'id' => 1, 'title' => '发布须知！',
            ],
            [
                'id' => 2, 'title' => '关于我们',
            ]
        ];
        //多级栏目
        $column = $pcloumn;
        // foreach ($column as &$val) {
        //     $val['child'] = $columns->getList(['status' => 1, 'pid' => $val['id']]);
        // }
        $dropDownItems = [];
        foreach ($column as $index=>&$val) {
            $dropDownItems[$index][] = ['text' => $val['name'], 'value' => $val['id']];
            $child = $columns->getList(['status' => 1, 'pid' => $val['id']]);
            foreach ($child as $c) {
                $dropDownItems[$index][] = ['text' => $c['name'], 'value' => $c['id']];
            }
        }
        $data['columns'] = $dropDownItems;
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
