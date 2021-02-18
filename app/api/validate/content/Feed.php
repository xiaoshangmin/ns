<?php

namespace app\api\validate\content;

use think\Validate;
use app\common\model\Content;

class Feed extends Validate
{
    /**
     * 验证规则.
     */
    protected $rule = [
        'mobile' => 'require|regex:1\d{10}$',
        'column_ids' => 'require',
        'contacts' => 'require',
        'address'    => 'require',
        'content'  => 'msgSecCheck:content'
    ];

    /**
     * 提示消息.
     */
    protected $message = [
        'mobile.require' => '请输入手机号',
        'mobile.regex' => '错误的手机号',
        'column_ids.require' => '栏目错误',
        'contacts.require' => '请输入联系人',
        'address.require' => '请选择地址信息',
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
        'add'  => ['mobile', 'column_id', 'contacts', 'address', 'content'],
        'edit' => ['mobile', 'contacts', 'address', 'content'],
    ];

    public function __construct()
    {
        parent::__construct();
    }
}
