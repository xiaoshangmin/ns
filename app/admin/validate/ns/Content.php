<?php

namespace app\admin\validate\ns;

use think\Validate;

class Content extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'pcolumn_id'=>'require',
        'mobile'=>'require',
        'contacts'=>'require',
        'address'=>'require',
        'content'=>'require',
    ];
    /**
     * 提示消息
     */
    protected $message = [
        'pcolumn_id.require' => '请选择栏目',
        'content.require' => '请输入内容',
    ];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => [],
        'edit' => [],
    ];
    
}
