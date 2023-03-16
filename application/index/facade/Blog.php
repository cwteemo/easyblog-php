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

    public function getList($data)
    {
        $pageSize = 5;
        if (!empty($data['pageSize'])) {
            $pageSize = $data['pageSize'];
        }
        $pageNumber = 1;
        if (!empty($data['pageNo'])) {
            $pageNumber = $data['pageNo'];
        }
        $model = new \app\index\model\Blog();
        //搜索字段
        $search_fields = [
            [
                'field' => 'title',
                'type' => 2
            ],
            [
                'field' => 'status',
                'type' => 1
            ],
            [
                'field' => 'category_id',
                'type' => 1
            ],
        ];
        foreach ($search_fields as $field_info) {
            $field = $field_info['field'];
            $op = $field_info['type'];
            if (isset($data[$field]) && $data[$field] !='') {
                //精确
                if ($op == 1) {
                    $model->where($field, $data[$field]);
                }
                //模糊
                if ($op == 2) {
                    $model->whereLike($field, "%$data[$field]%");
                }
                //范围
                if ($op == 3) {
                    $model->whereIn($field, $data[$field]);
                }
            }
        }
        $option = $model->getOptions();
        $list = $model->page($pageNumber, $pageSize)->select();
        $cloneModel = new \app\index\model\Blog();
        $total = $cloneModel->options($option)->count();
        return ['list' => $list, 'totalCount' => $total, 'pageSize' => $pageSize, 'pageNo' => $pageNumber];
    }
}