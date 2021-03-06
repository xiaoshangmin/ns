<?php

namespace app\api\controller\content;

use app\common\controller\Api;
use think\exception\ValidateException;
use app\common\model\{Content, LikeLog, Orders, Columns, ColumnContent, TopConfig};
use think\facade\{Config, Log, Env, Db};
use EasyWeChat\Factory;
use Geohash;

/**
 * 首页接口.
 */
class Feed extends Api
{
    protected $noNeedLogin = ['lists', 'nearby', 'detail'];
    protected $noNeedRight = ['lists', 'nearby', 'detail'];
    public $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new Content();
    }

    public function lists()
    {
        $page = $this->request->post('p/d') ?: 1;
        $pageSize = $this->request->post('ps/d') ?: 10;
        $columnId = $this->request->post('columnId/d') ?: 0;
        $keyword = $this->request->post('keyword/s');
        if ($keyword) {
            $list = $this->model->getListByFullIndex(
                $this->auth->uid ?: 0,
                $columnId,
                $keyword,
                $page,
                $pageSize
            );
        } else {
            $list = $this->model->getHomeList(
                $this->auth->uid ?: 0,
                ['column_id' => $columnId, 'keyword' => $keyword],
                $page,
                $pageSize
            );
        }
        $this->success('ok', ['list' => $list]);
    }


    public function nearby()
    {
        $page = $this->request->post('p/d') ?: 1;
        $pageSize = $this->request->post('ps/d') ?: 10;
        $lat = $this->request->post('lat/f') ?: 0;
        $lng = $this->request->post('lng/f') ?: 0;
        $geohash = '';
        if ($lat && $lng) {
            $geohash = new Geohash();
            $geohash = $geohash->encode($lat, $lng);
            //附近，参数n代表Geohash，精确的位数，也就是大概距离；n=6时候，大概为附近1千米
            $geohash = substr($geohash, 0, 5);
        }
        $list = $this->model->getNearBy(
            $this->auth->uid ?: 0,
            ['geohash' => $geohash],
            $page,
            $pageSize
        );
        $this->success('ok', ['list' => $list]);
    }

    public function detail()
    {
        //ALTER TABLE ns_content ADD FULLTEXT INDEX ft_index (content) WITH PARSER ngram;
        $cid = $this->request->get('cid/d');
        $detail = $this->model->getById($cid, $this->auth->uid ?: 0);
        // $shareTitle = isset($detail['tags'][1]) ?$detail['tags'][1]:$detail['tags'][0];
        // $content = mb_substr($detail['content'],0,50);
        // $shareTitle = "【{$shareTitle}】{$content}";
        // $detail['shareTitle'] = $shareTitle;
        //管理员可见内容
        $adminUids = Env::get('admin.uid', 0);
        $adminUids = explode(',', $adminUids);
        if (!in_array($this->auth->uid, $adminUids)) {
            $detail['extra'] = [];
        }
        $this->model->viewInc($cid);
        $this->success('ok', $detail);
    }

    public function like()
    {
        //加锁防止频繁点击
        $uid = $this->auth->uid;
        if (false === limit_operation_count("like:{$uid}", 5, 60)) {
            $this->error('操作过于频繁,请稍候再试');
        }
        $cid = $this->request->post('cid/d');
        if (false === (new LikeLog())->isLike($uid, $cid, LikeLog::CONTENT_LIKE_TYPE)) {
            $this->model->likeInc($cid);
            (new LikeLog())->addLog($uid, $cid, LikeLog::CONTENT_LIKE_TYPE);
        }
        $this->success();
    }

    public function unlike()
    {
        //加锁防止频繁点击
        $uid = $this->auth->uid;
        if (false === limit_operation_count("like:{$uid}", 5, 60)) {
            $this->error('操作过于频繁,请稍候再试');
        }
        $cid = $this->request->post('cid/d');
        $this->model->likeDec($cid);
        (new LikeLog())->removeLog($uid, $cid, LikeLog::CONTENT_LIKE_TYPE);
        $this->success();
    }

    public function submit()
    {
        if (true === $this->auth->isBlock()) {
            $this->error('此账号已被封号');
        }
        //加锁
        $uid = $this->auth->uid;
        if (false === lock("submit:{$uid}", 5)) {
            $this->error('操作过于频繁,请稍候再试');
        }
        $params = $this->request->post();
        $log = 'submit:' . json_encode($params, JSON_UNESCAPED_UNICODE);
        Log::record($log);
        if ($params) {
            // 是否委托发布
            if (!$params['entrust']) {
                try {
                    validate(\app\api\validate\content\Feed::class)->check($params);
                } catch (ValidateException $e) {
                    $this->error($e->getMessage());
                }
            } else {
                if (empty($params['mobile'])) {
                    $this->error('请输入手机号！');
                }
                $params['status'] = 0;
            }
            $params['uid'] = $uid;
            //订单总金额
            $orderAmount = 0;
            //获取最后一个栏目
            $postCloumnIds = explode(',', $params['column_ids']);
            $columnIds = $postCloumnIds;
            $cloumnId = array_pop($columnIds);
            $columnInfo = Columns::find($cloumnId);
            $orderAmount = bcadd($orderAmount, $columnInfo['price'], 2);

            unset($cloumnId);
            //保存内容主体信息
            $result = $this->model->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }
            $params['cid'] = $this->model->id;
            //栏目关联内容
            foreach ($postCloumnIds as $cloumnId) {
                (new ColumnContent())->addRelateContent($cloumnId, $params['cid'], [
                    'top' => $this->model->top,
                    'expiry_time' => $this->model->expiry_time,
                ]);
            }
            //获取置顶类型对应的价格
            if (isset($params['top_id']) && !empty($params['top_id'])) {
                $topInfo = TopConfig::find($params['top_id']);
                $orderAmount = bcadd($orderAmount, $topInfo['price'], 2);
            }
            $params['orderAmount'] = floatval($orderAmount);
            $params['order_type'] = Orders::ORDER_TYPE_SUBMIT;
            //新增订单
            if ($params['orderAmount'] && $this->_need_pay) {
                $order = new Orders();
                $ret = $order->add($params);
                //生成预支付信息
                $result = $this->getPrepayInfo([
                    'out_trade_no' => $ret->order_sn,
                    'openid' => $this->auth->openid,
                    'total_fee' => $ret->order_amount,
                ]);
                $log = 'getPrepayInfo:' . json_encode($result, JSON_UNESCAPED_UNICODE);
                Log::record($log);
                if ($result) {
                    $this->success('ok', $result, 1000);
                }
            } else {
                //免费发布
                $this->model->changePayStatus($params['cid'], Orders::PAY_SUCCESS);
            }
            $this->success('ok');
        }
        $this->error();
    }


    public function modify()
    {
        if (true === $this->auth->isBlock()) {
            $this->error('此账号已被封号');
        }
        //加锁
        $uid = $this->auth->uid;
        if (false === lock("submit:{$uid}", 10)) {
            $this->error('操作过于频繁,请稍候再试');
        }
        $params = $this->request->post();
        $cid = $params['id'] ?? 0;
        if (empty($cid)) {
            $this->error('内容不存在');
        }
        $content = Content::where('id', $cid)->find();
        if (empty($content)) {
            $this->error('内容不存在');
        }
        if ($content->uid != $this->auth->uid) {
            $this->error('禁止操作');
        }

        $log = 'modify:' . json_encode($params, JSON_UNESCAPED_UNICODE);
        Log::record($log);
        if ($params) {
            try {
                validate(\app\api\validate\content\Feed::class)->scene('edit')->check($params);
            } catch (ValidateException $e) {
                $this->error($e->getMessage());
            }
            $params['uid'] = $uid; 
            //修改内容主体信息
            $result = $content->allowField(['content', 'address', 'contacts', 'lat', 'lng', 'mobile', 'pictures'])
                ->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }
            $this->success('ok');
        }
        $this->error();
    }

    /**
     * 付费刷新文章
     *
     * @return void
     * @author xsm
     * @since 2020-12-18
     */
    public function refresh()
    {
        $params = $this->request->post();
        $cid = $params['id'] ?? 0;
        if (empty($cid)) {
            $this->error('内容不存在');
        }
        $content = $this->model->field('id,uid,column_ids')->where('id', $cid)->find();
        if (empty($content)) {
            $this->error('内容不存在');
        }
        if ($content['uid'] != $this->auth->uid) {
            $this->error('禁止操作');
        }
        $postCloumnIds = explode(',', $content['column_ids']);
        $columnIds = $postCloumnIds;
        $cloumnId = array_pop($columnIds);
        $columnInfo = Columns::find($cloumnId);
        if (empty($columnInfo)) {
            $this->error('关联栏目不存在或已下架');
        }
        $order['orderAmount'] = floatval($columnInfo['refresh_price']);
        $order['uid'] = $this->auth->uid;
        $order['top_id'] = 0;
        $order['order_type'] = Orders::ORDER_TYPE_REFRESH;
        $order['cid'] = $content['id'];
        $log = 'refresh:' . json_encode($order, JSON_UNESCAPED_UNICODE);
        Log::record($log);
        //新增订单
        if ($order['orderAmount']) {
            $ret = (new Orders())->add($order);
            //生成预支付信息
            $result = $this->getPrepayInfo([
                'out_trade_no' => $ret->order_sn,
                'openid' => $this->auth->openid,
                'total_fee' => $ret->order_amount,
            ]);
            $log = __FUNCTION__ . ':getPrepayInfo:' . json_encode($result, JSON_UNESCAPED_UNICODE);
            Log::record($log);
            if ($result) {
                $this->success('ok', $result, 1000);
            }
        } else {
            //免费发布
            $this->model->changePayStatus($order['cid'], Orders::PAY_SUCCESS);
        }
        $this->success('ok');
    }

    /**
     * 付费置顶文章
     *
     * @return void
     * @author xsm
     * @since 2020-12-18
     */
    public function top()
    {
        $params = $this->request->post();
        $cid = $params['id'] ?? 0;
        $topId = $params['top_id'] ?? 0;
        $content = $this->model->field('id,uid,column_ids')->where('id', $cid)->find();
        if (empty($content)) {
            $this->error('内容不存在');
        }
        if ($content['uid'] != $this->auth->uid) {
            $this->error('禁止操作');
        }
        $postCloumnIds = explode(',', $content['column_ids']);
        $columnIds = $postCloumnIds;
        $cloumnId = array_pop($columnIds);
        $columnInfo = Columns::find($cloumnId);
        if (empty($columnInfo)) {
            $this->error('关联栏目不存在或已下架');
        }

        $topInfo = TopConfig::find($params['top_id']);
        $order['orderAmount'] =  floatval($topInfo['price']);
        $order['uid'] = $this->auth->uid;
        $order['top_id'] = $topId;
        $order['order_type'] = Orders::ORDER_TYPE_TOP;
        $order['cid'] = $content['id'];
        $log = 'top:' . json_encode($order, JSON_UNESCAPED_UNICODE);
        Log::record($log);
        //新增订单
        if ($order['orderAmount']) {
            $ret = (new Orders())->add($order);
            //生成预支付信息
            $result = $this->getPrepayInfo([
                'out_trade_no' => $ret->order_sn,
                'openid' => $this->auth->openid,
                'total_fee' => $ret->order_amount,
            ]);
            $log = 'getPrepayInfo:' . json_encode($result, JSON_UNESCAPED_UNICODE);
            Log::record($log);
            if ($result) {
                $this->success('ok', $result, 1000);
            }
        } else {
            //免费发布
            $this->model->changePayStatus($order['cid'], Orders::PAY_SUCCESS);
        }
        $this->success('ok');
    }

    /**
     * 获取小程序支付信息
     *
     * @param array $data
     * @return array
     * @author xsm
     * @since 2020-09-19
     */
    public function getPrepayInfo(array $data): array
    {
        $config = Config::get('api.miniprogram.ns');
        $app = Factory::payment($config);
        $totalFee = bcmul($data['total_fee'], 100);
        if ($data['openid'] == 'otthZ5JkDfCuIBojzSAaB1c30cYc') {
            $totalFee = 1;
        }
        $result = $app->order->unify([
            'body' => '南沙小程序-发布内容',
            'out_trade_no' => $data['out_trade_no'],
            'total_fee' => $totalFee,
            'trade_type' => 'JSAPI',
            'openid' => $data['openid'],
        ]);
        $log = __FUNCTION__ . ':getPrepayInfo:' . json_encode($result, JSON_UNESCAPED_UNICODE);
        Log::record($log);
        if (
            isset($result['return_code']) && 'SUCCESS' == $result['return_code']
            && isset($result['result_code']) && 'SUCCESS' == $result['result_code']
        ) {
            $jssdk = $app->jssdk;
            $config = $jssdk->bridgeConfig($result['prepay_id'], false); // 返回数组
            return $config;
        }
        return [];
    }

    /**
     * 删除
     */
    public function del($ids = '')
    {
        if ($ids) {
            $pk       = $this->model->getPk();
            $where = [[$pk, 'in', $ids], ['uid', '=', $this->auth->uid]];
            $list = $this->model->where($where)->select();

            $count = 0;
            Db::startTrans();

            try {
                foreach ($list as $v) {
                    $count += $v->delete();
                }
                Db::commit();
            } catch (\PDOException $e) {
                Db::rollback();
                $this->error($e->getMessage());
            } catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage());
            }
            if ($count) {
                $this->success();
            } else {
                $this->error();
            }
        }
        $this->error();
    }
}
