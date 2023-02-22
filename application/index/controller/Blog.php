<?php


namespace app\index\controller;


use app\index\lib\Res;
use app\index\facade\BlogTeamUser;
use app\index\validate\LoginValidate;
use think\captcha\Captcha;
use think\Config;

class Blog extends Base
{
    public function getCheckCode()
    {
        $array = [
            'codeSet' => '123456789',
            'length' => 3,
        ];
        //$array = (array)Config::get('captcha');
        $captcha = new Captcha($array);
        return $captcha->entry();
    }

}