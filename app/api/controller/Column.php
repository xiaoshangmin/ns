<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\Columns;
use think\facade\Cache;

/**
 * 首页接口.
 */
class Column extends Api
{
    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }
    /**
     * 专栏列表
     *
     * @return void
     * @author xsm
     * @since 2020-09-19
     */
    public function list()
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

    /**
     * 子栏目
     *
     * @return void
     * @author xsm
     * @since 2020-10-12
     */
    public function child()
    {
        $pid = $this->request->post('pid/d') ?: 0;
        $key = "column:child:id:".$pid;
        $redis = Cache::store('redis')->handler();
        $cache = $redis->get($key);
        if($cache){
            $this->success('ok',json_decode($cache,true));
        }
        //一级栏目
        $columns = new Columns();
        $columnlist = $columns->getList(['status' => 1, 'pid' => $pid]);
        foreach($columnlist as &$c){
            $c['childlist'] = $columns->getList(['status' => 1, 'pid' => $c['id']]);
        }
        // $columnlist = [['id' => $pid, 'name' => '全部']];
        // $columnlist = array_merge($columnlist, $child);
        $redis->set($key,json_encode($columnlist,JSON_UNESCAPED_UNICODE),['ex'=>300]);
        $this->success('ok',  $columnlist);
    }
}
