<?php

namespace app\admin\controller\ns;

use app\common\controller\Backend;
use think\facade\Db;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Column extends Backend
{
    
    /**
     * Column模型对象
     * @var \app\admin\model\Column
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Column;
        $this->view->assign("statusList", $this->model->getStatusList());
        $pcolumnList = $this->model->getPcloumnList();
        $pcolumnList = array_column($pcolumnList,'name','id');
        $pcolumnList[0] = __('None');
        $this->view->assign("pcolumnList", $pcolumnList);
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */

    
    public function cxselect()
    { 
        $pid = $this->request->get('pid');
        $where = ['status' => 1];
        $categorylist = null;
         
        if ($pid !== '') {
            $where['pid'] = $pid;
            $categorylist = Db::name('column')->where($where)->field('id as value,name,template')->order('id asc')->select();
        }
        $this->success('ok', null, $categorylist);
    }

    /**
     * 查看.
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            $list  = $this->model->getTreeList();
            // echo '<pre>';
            // print_r($list);
            // exit();
            $total = count($list);

            $result = ['total' => $total, 'rows' => $list];

            return json($result);
        }

        return $this->view->fetch();
    }

}
