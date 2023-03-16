<?php


namespace app\index\facade;


use app\index\model\BlogCategory;

class Category extends Base
{
    public function getList()
    {
        $model = new BlogCategory();
        return $model->getList();
    }
}