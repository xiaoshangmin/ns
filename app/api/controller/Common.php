<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\model\{Ads, Navigation, Columns, Notice};
use think\facade\Cache;

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
        // $key = "mini:common:config";
        // $redis = Cache::store('redis')->handler();
        // $cache = $redis->get($key);
        // if ($cache) {
        //     $this->success('ok',json_decode($cache,true));
        // }
        $help = (new Notice())->getList(['status' => 1, 'type' => 2], 1, 1);
        $notice = (new Notice())->getList(['status' => 1, 'type' => 3], 1, 1);
        $data = ['help' => [], 'need_pay' => $this->_need_pay];
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
        $about = (new Notice())->getList(['status' => 1, 'type' => 4], 1, 1);
        $data['about'] = $about[0] ?? [];
        $publishNotice = (new Notice())->getList(['status' => 1, 'type' => 3], 1, 1);
        $data['publish_notice'] = $publishNotice[0] ?? [];
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
        // $redis->set($key, json_encode($data, JSON_UNESCAPED_UNICODE), ['ex' => 300]);
        $this->success('ok', $data);
    }
}
