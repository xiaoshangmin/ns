<?php
return [
    'miniprogram' => [
        'ns' => [
            'app_id' => 'wx09084239f923da22',
            'secret' => '49874681039653dcf4545d65485d837e',
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',

            'log' => [
                'level' => 'debug',
                'file' => __DIR__ . '/wechat.log',
            ],
        ]
    ]
];
