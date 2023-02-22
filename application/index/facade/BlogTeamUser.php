<?php


namespace app\index\facade;


class BlogTeamUser extends Base
{

    public function getUserInfo($where){
        $model = new \app\index\model\BlogTeamUser();
        return $model->where($where)->find();
    }
}