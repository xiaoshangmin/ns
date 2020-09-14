<?php

namespace app\api\validate\content;

use think\Validate;

class Feed extends Validate
{
    /**
     * 验证规则.
     */
    protected $rule = [
        'mobile' => 'require|regex:1\d{10}$',
        'column_id' => 'require|number',
        'contacts' => 'require',
        'location'    => 'require',
    ];

    /**
     * 提示消息.
     */
    protected $message = [
        'mobile.require' => '请输入手机号',
        'mobile.regex' => '错误的手机号',
        'column_id.require' => '栏目错误',
        'contacts.require' => '请输入联系人',
        'location.require' => '请选择地址信息',
    ];

    /**
     * 字段描述.
     */
    protected $field = [];

    /**
     * 验证场景.
     */
    protected $scene = [
        'add'  => ['mobile', 'column_id', 'contacts', 'address'],
        'edit' => ['mobile', 'column_id', 'contacts', 'address'],
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
