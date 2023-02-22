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
}