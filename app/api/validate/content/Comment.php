<?php

namespace app\api\validate\content;

use think\Validate;
use app\common\model\Content;

class Comment extends Validate
{
    /**
     * 验证规则.
     */
    protected $rule = [
        'content' => 'require|msgSecCheck:content',
        'cid' => 'require',
    ];

    /**
     * 提示消息.
     */
    protected $message = [
        'content.require' => '请输入内容',
        'cid.require' => '内容信息错误，请重新进入',
    ];

    /**
     * 字段描述.
     */
    protected $field = [];

    protected function msgSecCheck($value, $rule, $data = [])
    {
        $data = (new Content())->msgSecCheck($value);
        if ($data['code']) {
            return $data['msg'];
        }
        return true;
    }

    /**
     * 验证场景.
     */
    protected $scene = [
        'add'  => ['content', 'cid'],
        'edit' => ['content', 'cid'],
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
