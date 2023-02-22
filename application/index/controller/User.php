<?php


namespace app\index\controller;


use app\index\facade\BlogTeamUser;
use app\index\lib\Res;
use app\index\validate\LoginValidate;

class User extends Base
{

    public function postLogin()
    {
        $param = postParams();
        (new LoginValidate())->goCheck();
        if (!captcha_check($param['checkCode'])) {
            Res::returnErr('验证码错误');
        }

        $model = new BlogTeamUser();
        $where = $model->filterArray(['account' => 'phone', 'password'], $param);
        $info = $model->getUserInfo($where);
        if (!$info) {
            Res::returnErr($param);
        }
        return $info;
    }

}