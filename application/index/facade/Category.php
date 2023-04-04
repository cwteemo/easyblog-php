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

    public function getCategoryName($id){
        $model = new BlogCategory();
        return $model->where(['category_id'=>$id])->value('category_name');
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


    public function del($where)
    {
        $model = new BlogCategory();
        $model::destroy($where);
        return true;
    }

    public function update_sort($list)
    {
        foreach ($list as $key => &$item) {
            $item['sort'] = $key + 1;
        }
        $model = new BlogCategory();
        $model->allowField(true)->isUpdate(true)->saveAll($list);
        return $model;
    }


}