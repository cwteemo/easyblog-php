<?php


namespace app\index\controller;


class Category extends Base
{

    public function postIndex(){
        $model = new \app\index\facade\Category();
        return $model->getList();
    }

    public function postUpdate(){
        $param = postParams();
        $model = new \app\index\facade\Category();
        return $model->update($param);
    }

    public function postAdd(){
        $param = postParams();
        $model = new \app\index\facade\Category();
        return $model->add($param);
    }

    public function postDel(){
        $param = postParams();
        $model = new \app\index\facade\Category();
        return $model->del($param);
    }

    public function postUpdate_sort(){
        $param = postParams();
        $model = new \app\index\facade\Category();
        return $model->update_sort($param);
    }

    public function postSelect()
    {
        $model = new \app\index\facade\Category();
        return $model->getList();
    }
}