<?php
/**
 *  ==============================================================
 *  Created by PhpStorm.
 *  User: Ice
 *  邮箱: ice@sbing.vip
 *  网址: https://sbing.vip
 *  Date: 2020/7/7 下午3:54
 *  ==============================================================
 */

namespace addons\cloudstore\library\qiniu\plugins;

use League\Flysystem\Plugin\AbstractPlugin;

class FetchFile extends AbstractPlugin
{
    public function getMethod()
    {
        return 'fetch';
    }

    public function handle($path, $url)
    {
        return $this->filesystem->getAdapter()->fetch($path, $url);
    }
}
