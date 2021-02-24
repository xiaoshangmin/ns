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
    public function multList()
    {
        // $key = "mini:column:list";
        // $redis = Cache::store('redis')->handler();
        // $cache = $redis->get($key);
        // if ($cache) {
        //     $this->success('ok',json_decode($cache,true));
        // }
        //一级栏目
        $columns = new Columns();
        $pcloumn = $columns->getList(['status' => 1, 'pid' => 0]);
        $child = [];
        //二级栏目 
        foreach ($pcloumn as &$val) {
            $child[$val['id']] = $columns->getList(['status' => 1, 'pid' => $val['id']]);
        }
        $data = ['pcloumn' => $pcloumn, 'child' => $child];
        // $redis->set($key, json_encode($data, JSON_UNESCAPED_UNICODE), ['ex' => 300]);
        $this->success('ok', $data);
    }



    /**
     * 全部子栏目
     *
     * @return void
     * @author xsm
     * @since 2020-10-12
     */
    public function child()
    {
        $pid = $this->request->post('pid/d') ?: 0;
        // $key = "column:child:id:".$pid;
        // $redis = Cache::store('redis')->handler();
        // $cache = $redis->get($key);
        // if($cache){
        //     $this->success('ok',json_decode($cache,true));
        // }
        //一级栏目
        $columns = new Columns();
        $columnlist = $columns->getList(['status' => 1, 'pid' => $pid]);
        foreach ($columnlist as &$c) {
            $childlist = $columns->getList(['status' => 1, 'pid' => $c['id']]);
            $p = $c;
            $p['name'] = '全部';
            array_unshift($childlist, $p); //把父级也搞进去
            $c['childlist'] = array_chunk($childlist, 3);
        }
        // $columnlist = [['id' => $pid, 'name' => '全部']];
        // $columnlist = array_merge($columnlist, $child);
        // $redis->set($key,json_encode($columnlist,JSON_UNESCAPED_UNICODE),['ex'=>300]);
        $this->success('ok',  $columnlist);
    }

    public function list()
    {
        $pid = $this->request->post('pid/d') ?: 0;
        $columns = new Columns();
        $list = $columns->getList(['status' => 1, 'pid' => $pid]);
        $hasTree = $list ? (3 == $list[0]['level'] ?: false) : false;
        $data = ['list' => $list, 'hasTree' => $hasTree];
        $this->success('ok', $data);
    }
}
