<?php

namespace addons\cloudstore;

use addons\cloudstore\library\CloudDriver;
use app\common\library\Menu;
use think\Addons;
use think\facade\Config;

/**
 * 云存储
 */
class Cloudstore extends Addons
{

    /**
     * 插件安装方法
     *
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     *
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 插件启用方法
     *
     * @return bool
     */
    public function enable()
    {
        return true;
    }

    /**
     * 插件禁用方法
     *
     * @return bool
     */
    public function disable()
    {
        return true;
    }

    /**
     * 添加命名空间
     */
    public function appInit()
    {
        $cfg                              = Config::get('filesystem');
        $cfg['disks'][CloudDriver::class] = [
            'type'       => CloudDriver::class,
            'root'       => app()->getRootPath().'public',
            'url'        => '/',
            'visibility' => 'public',
        ];
        Config::set($cfg, 'filesystem');
        //引入云存储包
        require_once ADDON_PATH.'cloudstore'.DS.'vendor'.DS.'autoload.php';
    }

    public function uploadInit()
    {
        $cfg    = get_addon_config('cloudstore');
        $domain = substr($cfg['domain'], -1) !== '/' ? $cfg['domain'].'/' : $cfg['domain'];
        return [
            'driver' => CloudDriver::class,
            'cdnurl' => $domain,
        ];
    }

}
