<?php
/**
 *  ==============================================================
 *  Created by PhpStorm.
 *  User: Ice
 *  邮箱: ice@sbing.vip
 *  网址: https://sbing.vip
 *  Date: 2020/7/7 下午4:34
 *  ==============================================================
 */

namespace addons\cloudstore\library;


use addons\cloudstore\library\aliyun\OssAdapter;
use addons\cloudstore\library\qcloud\QcloudAdapter;
use addons\cloudstore\library\qiniu\QiniuAdapter;
use League\Flysystem\AdapterInterface;
use think\filesystem\Driver;

class CloudDriver extends Driver
{
    /**
     * @return AdapterInterface
     * @throws \Exception
     */
    protected function createAdapter(): AdapterInterface
    {
        $config = get_addon_config('cloudstore');
        $driver = $config['type'];
        switch ($driver) {
            case 'qiniu':
                return new QiniuAdapter($config[$driver]);
            case 'aliyun':
                return new OssAdapter($config[$driver]);
            case 'qcloud':
                return new QcloudAdapter($config[$driver]);
        }
        return null;
    }
}