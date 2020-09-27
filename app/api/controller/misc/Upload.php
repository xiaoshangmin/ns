<?php

namespace app\api\controller\Misc;

use app\common\controller\Api;
use \think\facade\Filesystem;

/**
 * 会员接口.
 */
class Upload extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 七牛上传token
     *
     * @return void
     * @author xsm
     * @since 2020-09-13
     */
    public function uptoken()
    {
        $driver = Filesystem::disk('addons\cloudstore\library\CloudDriver');
        $returnBody = '{"key": $(key),"imageInfo": $(imageInfo)}';
        $policy = ['returnBody' => $returnBody];
        $uptoken = $driver->getAdapter()->getUploadToken(null, 3600, $policy);
        return json_encode(['uptoken' => $uptoken]);
    }
}
