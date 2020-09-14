<?php
/**
 *  ==============================================================
 *  Created by PhpStorm.
 *  User: Ice
 *  邮箱: ice@sbing.vip
 *  网址: https://sbing.vip
 *  Date: 2020/7/7 下午4:04
 *  ==============================================================
 */

namespace addons\cloudstore\library\aliyun;

use League\Flysystem\Config;

class Supports
{

    private $flashData = null;


    public function setFlashData($data = null)
    {
        $this->flashData = $data;
    }

    public function getFlashData()
    {
        $flash           = $this->flashData;
        $this->flashData = null;
        return $flash;
    }

}
