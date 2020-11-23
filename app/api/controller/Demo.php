<?php

namespace app\api\controller;

use app\common\controller\Api;
use Geohash;
use Elasticsearch\ClientBuilder;

/**
 * 示例接口.
 */
class Demo extends Api
{
    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['test2'];

    /**
     * 测试方法.
     *
     * @ApiTitle    (测试名称)
     * @ApiSummary  (测试描述信息)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/demo/test/id/{id}/name/{name})
     * @ApiHeaders  (name=token, type=string, required=true, description="请求的Token")
     * @ApiParams   (name="id", type="integer", required=true, description="会员ID")
     * @ApiParams   (name="name", type="string", required=true, description="用户名")
     * @ApiParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
     * 'code':'1',
     * 'msg':'返回成功'
     * })
     */
    public function create()
    {
        //创建索引
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(['elasticsearch:9200'])->build();
        $params = [
            'index' => 'databases', //索引名（相当于mysql的数据库）
            'body' => [
                'settings' => [
                    'number_of_shards' => 3, //指索引要做多少个分片，只能在创建索引时指定，后期无法修改
                    'number_of_replicas' => 0, //指每个分片有多少个副本，后期可以动态修改，如果只有一台机器，设置为0
                ],
                'mappings' => [
                    '_source' => [ //  存储原始文档
                        'enabled' => true
                    ],
                    'properties' => [
                        'name' => [ //字段1
                            'type' => 'text', //类型 text、integer、float、double、boolean、date
                            'index' => 'true', //是否索引
                        ],
                        'age' => [ //字段2
                            'type' => 'integer',
                        ],
                        'sex' => [ //字段3
                            'type' => 'keyword',
                            'index' => 'false',
                        ],
                    ]
                ],
            ]
        ];

        $response = $client->indices()->create($params);
        $this->success('返回成功', $response);
    }
    /**
     * 搜索
     *
     * @return void
     * @author xsm
     * @since 2020-11-21
     */
    public function search()
    {
        $client = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(['elasticsearch:9200'])->build();
        $params = [
            'index' => 'databases',
            'body' => [
                'query' => [
                    'match' => [
                        'age' => 232
                    ]
                ],
                'sort' => [
                    'age' => [
                        'order' => 'desc'
                    ]
                ]
            ]
        ];

        $response = $client->search($params);
        $this->success('返回成功', $response);
    }

    /**
     * 添加数据
     *
     * @return void
     * @author xsm
     * @since 2020-11-21
     */
    public function add()
    {
        $client = ClientBuilder::create()->setHosts(['elasticsearch:9200'])->build();
        $params = [
            'index' => 'databases',
            'body'  => ['name' => 'jfja几点', 'age' => 232, 'sex' => '女方家打开撒酒疯多少啊覅打覅打好覅打']
        ];

        $response = $client->index($params);
        $this->success('返回成功', $response);
    }

    /**
     * 删除数据库
     *
     * @return void
     * @author xsm
     * @since 2020-11-21
     */
    public function delete()
    {
        $client = ClientBuilder::create()->setHosts(['elasticsearch:9200'])->build();
        $params = [
            'index' => 'databases',
        ];
        $response = $client->indices()->delete($params);
        $this->success('返回成功', $response);
    }

    /**
     * 无需登录的接口.
     */
    public function test1()
    {
    }

    /**
     * 需要登录的接口.
     */
    public function test2()
    {
        $this->success('返回成功', ['action' => 'test2']);
    }

    /**
     * 需要登录且需要验证有相应组的权限.
     */
    public function test3()
    {
        $this->success('返回成功', ['action' => 'test3']);
    }
}
