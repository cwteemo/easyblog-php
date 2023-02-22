<?php


namespace app\index\controller;


use think\captcha\Captcha;

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

    public function postIndex(){
        $model = new \app\index\facade\Blog();
        return $model->index();
    }

}