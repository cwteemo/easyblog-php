<?php


namespace app\index\validate;


class LoginValidate extends BaseValidate
{
    protected $rule = [
        'account' => 'require|isNotEmpty',
        'password' => 'require|isNotEmpty',
        'checkCode' => 'require|isNotEmpty',
    ];

    protected $message = [
        'account' => '账号不能为空',
        'password' => '密码不能为空',
        'checkCode' => '必须填写验证码',
    ];
}