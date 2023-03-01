<?php


namespace app\index\facade;


use app\index\model\BlogCategory;

class Blog extends Base
{

    public function index()
    {
        $model = new BlogCategory();
        return $model->select();
    }

    public function update($param)
    {
        $model = new BlogCategory();
        $model->data($param);
        $model->allowField(true)->isUpdate(true)->save();
        return $model;
    }

    public function add($param)
    {
        $model = new BlogCategory();
        $model->data($param);
        $model->allowField(true)->isUpdate(false)->save();
        return $model;
    }
}