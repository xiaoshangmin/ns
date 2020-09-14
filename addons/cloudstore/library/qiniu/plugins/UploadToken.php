<?php
/**
 *  ==============================================================
 *  Created by PhpStorm.
 *  User: Ice
 *  邮箱: ice@sbing.vip
 *  网址: https://sbing.vip
 *  Date: 2020/7/7 下午3:55
 *  ==============================================================
 */
namespace addons\cloudstore\library\qiniu\plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class UploadToken extends AbstractPlugin
{
    public function getMethod()
    {
        return 'getUploadToken';
    }

    public function handle($key = null, $expires = 3600, $policy = null, $strictPolice = null)
    {
        return $this->filesystem->getAdapter()->getUploadToken($key, $expires, $policy, $strictPolice);
    }
}
