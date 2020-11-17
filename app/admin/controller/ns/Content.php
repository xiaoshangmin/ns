<?php

namespace app\admin\controller\ns;

use app\common\controller\Backend;
use think\facade\Db;
use think\Exception;
use think\exception\PDOException;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Content extends Backend
{
    
    /**
     * Content模型对象
     * @var \app\admin\model\ns\Content
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\ns\Content;
        $this->view->assign("statusList", $this->model->getStatusList());
        $this->view->assign("topList", $this->model->getTopList());
        $this->view->assign("payStatusList", $this->model->getPayStatusList());
        $this->view->assign("isOnlineList", $this->model->getIsOnlineList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

     public function refresh($ids = "")
     {
        if ($ids) {
            $pk = $this->model->getPk(); 
            $list = $this->model->where($pk, 'in', $ids)->select();

            $count = 0;
            Db::startTrans();
            try {
                foreach ($list as $v) {
                    $count += $v->save(['create_time'=>time(),'update_time'=>time()]);
                }
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
     }

}
