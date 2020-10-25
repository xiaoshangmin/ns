<?php

namespace app\api\controller\Content;

use app\common\controller\Api;
use think\exception\ValidateException;
use app\common\model\{Content, LikeLog, Orders, Columns, ColumnContent, TopConfig};
use think\facade\{Config, Log, Cache};
use EasyWeChat\Factory;
use Geohash;

/**
 * 首页接口.
 */
class Feed extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = [];
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
        $type = $this->request->post('type/d') ?: 1;
        $keyword = $this->request->post('keyword/s');
        if ($keyword) {
            $list = $this->model->getListByFullIndex(
                $this->auth->uid,
                $columnId,
                $keyword,
                $page,
                $pageSize
            );
        } else {
            $list = $this->model->getHomeList(
                $this->auth->uid,
                ['column_id' => $columnId, 'type' => $type, 'keyword' => $keyword],
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
            $this->auth->uid,
            ['type' => 1, 'geohash' => $geohash],
            $page,
            $pageSize
        );
        $this->success('ok', ['list' => $list]);
    }

    public function detail()
    {
        //ALTER TABLE ns_content ADD FULLTEXT INDEX ft_index (content) WITH PARSER ngram;
        $cid = $this->request->get('cid/d');
        $detail = $this->model->getById($cid, $this->auth->uid);
        $this->model->viewInc($cid);
        $this->success('ok', $detail);
    }

    public function like()
    {
        //加锁防止频繁点击
        Cache::store('redis')->handler();
        $cid = $this->request->post('cid/d');
        if (false === (new LikeLog())->isLike($this->auth->uid, $cid, LikeLog::CONTENT_LIKE_TYPE)) {
            $this->model->likeInc($cid);
            (new LikeLog())->addLog($this->auth->uid, $cid, LikeLog::CONTENT_LIKE_TYPE);
        }
        $this->success();
    }

    public function unlike()
    {
        //加锁防止频繁点击
        Cache::store('redis')->handler();
        $cid = $this->request->post('cid/d');
        $this->model->likeDec($cid);
        (new LikeLog())->removeLog($this->auth->uid, $cid, LikeLog::CONTENT_LIKE_TYPE);
        $this->success();
    }

    public function submit()
    {
        //加锁
        $params = $this->request->post();
        $log = 'submit:' . json_encode($params, JSON_UNESCAPED_UNICODE);
        Log::record($log);
        if ($params) {
            try {
                validate(\app\api\validate\content\Feed::class)->check($params);
            } catch (ValidateException $e) {
                $this->error($e->getMessage());
            }
            $params['uid'] = $this->auth->uid;
            //订单总金额
            $orderAmount = 0;
            //获取栏目 
            $cloumns = explode(',', $params['column_ids']);
            foreach ($cloumns as $cloumnId) {
                $columnInfo = Columns::find($cloumnId);
                if (empty($columnInfo)) {
                    $this->error('栏目不存在或已下架');
                }
                $orderAmount = bcadd($orderAmount, $columnInfo['price']);
            }
            unset($cloumnId);
            $geohash = new Geohash();
            $params['geohash'] = $geohash->encode($params['lat'], $params['lng']);
            //保存内容主体信息
            $result = $this->model->save($params);
            if ($result === false) {
                $this->error($this->model->getError());
            }
            $params['cid'] = $this->model->id;
            //栏目关联内容
            foreach ($cloumns as $cloumnId) {
                (new ColumnContent())->addRelateContent($cloumnId, $params['cid'], [
                    'top' => $this->model->top,
                    'expiry_time' => $this->model->expiry_time,
                ]);
            }
            //获取置顶类型
            if (isset($params['top_id']) && !empty($params['top_id'])) {
                $topInfo = TopConfig::find($params['top_id']);
                $orderAmount = bcadd($orderAmount, $topInfo['price']);
            }
            $params['orderAmount'] = $orderAmount;
            //新增订单
            if ($orderAmount) {
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
        // if ($data['openid'] == 'otthZ5JkDfCuIBojzSAaB1c30cYc') {
        //     $totalFee = 1;
        // }
        $result = $app->order->unify([
            'body' => '南沙小程序-发布内容',
            'out_trade_no' => $data['out_trade_no'],
            'total_fee' => $totalFee,
            'trade_type' => 'JSAPI',
            'openid' => $data['openid'],
        ]);
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
}
