<?php

return [
    'autoload' => false,
    'hooks' => [
        'app_init' => [
            0 => 'cloudstore',
        ],
        'upload_init' => [
            0 => 'cloudstore',
        ],
    ],
    'route' => [
        '/example$' => 'example/index/index',
        '/example/d/[:name]' => 'example/demo/index',
        '/example/d1/[:name]' => 'example/demo/demo1',
        '/example/d2/[:name]' => 'example/demo/demo2',
        0 => [
            'addon' => 'cloudstore',
            'domain' => 'http://qgj8s2d2j.hn-bkt.clouddn.com',
            'rule' => [
            ],
        ],
    ],
    'service' => [
    ],
];