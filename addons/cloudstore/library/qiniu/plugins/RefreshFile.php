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

class RefreshFile extends AbstractPlugin
{
    public function getMethod()
    {
        return 'refresh';
    }

    public function handle($path = [])
    {
        return $this->filesystem->getAdapter()->refresh($path);
    }
}
