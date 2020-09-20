<?php

namespace app\api\validate\content;

use think\Validate;

class Comment extends Validate
{
    /**
     * 验证规则.
     */
    protected $rule = [
        'content' => 'require',
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
