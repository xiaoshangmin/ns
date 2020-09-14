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

class PrivateDownloadUrl extends AbstractPlugin
{
    public function getMethod()
    {
        return 'privateDownloadUrl';
    }

    public function handle($path, $expires = 3600)
    {
        return $this->filesystem->getAdapter()->privateDownloadUrl($path, $expires);
    }
}
