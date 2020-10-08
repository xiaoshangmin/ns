<?php
return [
    'miniprogram' => [
        'ns' => [
            'app_id' => 'wx09084239f923da22',
            'mch_id' => '1525986001',
            'secret' => '49874681039653dcf4545d65485d837e',
            'key'  => 'WeiXinXmQue123NanShaBuLuoA201899',   // API 密钥
            // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
            'cert_path'          =>  app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'library/wxcert/apiclient_cert.pem', // XXX: 绝对路径！！！！
            'key_path'           =>  app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'library/wxcert/apiclient_key.pem',      // XXX: 绝对路径！！！！
            // 下面为可选项
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
            'notify_url'         => 'http://buluo.wociao.xyz/api/wx/paynotify',
            'log' => [
                'level' => 'debug',
                'file' => app()->getRuntimePath() . 'log/' . date('Ymd') . '/wechat.log',
            ],
        ]
    ]
];
