<?php


namespace app\index\model;


class BlogCategory extends Base
{
    protected $table = 'blog_category';

    public function getList($where = []){
        if ($where){
            $this->where($where);
        }
        return $this->select();
    }
}