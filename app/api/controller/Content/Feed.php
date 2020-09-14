<?php

namespace app\api\controller\Content;

use app\common\controller\Api;
use think\facade\Validate;
use think\exception\ValidateException;
use app\common\model\Content;

/**
 * 首页接口.
 */
class Feed extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new Content();
    }

    public function submit()
    {
        // $columnId = $this->request->post('column_id');
        // $content = $this->request->post('content');
        // $picture = $this->request->post('picture');
        // $mobile = $this->request->post('mobile');
        // $contacts = $this->request->post('contacts');
        // $adress = $this->request->post('address');
        // $lng = $this->request->post('lng');
        // $lat = $this->request->post('lat');
        // $topId = $this->request->post('top_id');
        $params = $this->request->post();
        if ($params) {
            try {
                validate(\app\api\validate\content\Feed::class)->check($params);
            } catch (ValidateException $e) {
                $this->error($e->getMessage());
            }
            if ($params['top_id']) {
                $params['top'] = 1;
            }
            $params['uid'] = $this->auth->uid;
            $result = $this->model->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }

            // $this->success();
        }
        // $this->success('ok',$params);
    }
}
