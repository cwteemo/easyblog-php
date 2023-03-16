<?php


namespace app\index\controller;


class Category extends Base
{
    public function postList()
    {
        $model = new \app\index\facade\Category();
        return $model->getList();
    }

    public function postIndex()
    {
        $model = new \app\index\facade\Category();
        return ['list' => $model->getList()];
    }
}