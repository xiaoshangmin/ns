<?php

namespace app\api\controller\Content;

use app\common\controller\Api;
use think\exception\ValidateException;
use app\common\model\Comment as CommentModel;

/**
 * 首页接口.
 */
class Comment extends Api
{
    protected $noNeedLogin = [''];
    protected $noNeedRight = [''];
    public $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new CommentModel();
    }

    /**
     * 评论列表
     *
     * @return void
     * @author xsm
     * @since 2020-10-01
     */
    public function listPrimary()
    {
        $params = $this->request->post();
        $page = $this->request->post('p/d') ?: 1;
        $pageSize = $this->request->post('ps/d') ?: 10;
        $cid = $this->request->post('cid/d') ?: 0;
        $list = $this->model->getList($params, $page, $pageSize);
        $this->success('ok', ['list' => $list]);
    }

    public function detail()
    {
        $cid = $this->request->get('cid/d');
        $detail = $this->model->getById($cid);
        $this->success('ok', $detail);
    }

    public function submit()
    {
        $params = $this->request->post();
        if ($params) {
            try {
                validate(\app\api\validate\content\Comment::class)->check($params);
            } catch (ValidateException $e) {
                $this->error($e->getMessage());
            }
            $params['uid'] = $this->auth->uid;
            $result = $this->model->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }

            $this->success();
        }
        $this->error();
    }

  
}
