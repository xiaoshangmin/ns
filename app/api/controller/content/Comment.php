<?php

namespace app\api\controller\Content;

use app\common\controller\Api;
use think\exception\ValidateException;
use app\common\model\Comment as CommentModel;
use app\common\model\{Content, Wxuser};

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

    /**
     * 提交评论|回复评论
     *
     * @return void
     * @author xsm
     * @since 2020-11-04
     */
    public function submit()
    {
        if(true === $this->auth->isBlock())
        {
            $this->error('此账号已被封号');
        }
        $params = $this->request->post();
        if ($params) {
            try {
                validate(\app\api\validate\content\Comment::class)->check($params);
            } catch (ValidateException $e) {
                $this->error($e->getMessage());
            }
            $content = (new Content())->getBaseById($params['cid']);
            if (empty($content)) {
                $this->error('评论的内容不存在');
            }
            $params['uid'] = $this->auth->uid;
            $params['to_uid'] = $content['uid'];
            $result = $this->model->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }

            $this->success();
        }
        $this->error();
    }


    /**
     * 回复我的
     *
     * @return void
     * @author xsm
     * @since 2020-11-04
     */
    public function notifications()
    {
        $page = $this->request->post('p/d') ?: 1;
        $pageSize = $this->request->post('ps/d') ?: 10;
        $list = $this->model->replayMe($this->auth->uid, $page, $pageSize);
        Wxuser::where('uid', $this->auth->uid)->update(['last_read_comment_time' => time()]);
        $this->success('ok', $list);
    }
}
