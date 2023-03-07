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

    public function postUpdate(){
        $param = postParams();
        $model = new \app\index\facade\Blog();
        return $model->update($param);
    }

    public function postAdd(){
        $param = postParams();
        $model = new \app\index\facade\Blog();
        return $model->add($param);
    }

    public function postDel(){
        $param = postParams();
        $model = new \app\index\facade\Blog();
        return $model->del($param);
    }

    public function postUpdate_sort(){
        $param = postParams();
        $model = new \app\index\facade\Blog();
        return $model->update_sort($param);
    }

}