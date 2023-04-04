<?php


namespace app\index\facade;



class Blog extends Base
{
    public function index()
    {
        $model = new \app\index\model\Blog();
        return $model->select();
    }

    public function update($param)
    {
        $model = new \app\index\model\Blog();
        $param = $this->handle_data($param);
        $model->data($param);
        $model->allowField(true)->isUpdate(true)->save();
        return $model;
    }

    public function add($param)
    {
        $model = new \app\index\model\Blog();
        $param = $this->handle_data($param);
        $param['blog_id'] = $this->getBlogId();
        $model->data($param);
        $model->allowField(true)->isUpdate(false)->save();
        return $model;
    }

    private function handle_data($param)
    {
        if (!empty($param['category_id'])) {
            $category = new Category();
            $param['category_name'] = $category->getCategoryName($param['category_id']);
        }
        if (!empty($param['tag']) && is_array($param['tag'])) {
            $param['tag'] = json_encode($param['tag']);
        }
        return $param;
    }

    private function getBlogId(){
        return getRandChar(8);
    }

    public function del($where)
    {
        $model = new \app\index\model\Blog();
        $model::destroy($where);
        return true;
    }


    public function getList($data)
    {
        $pageSize = 10;
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
            if (isset($data[$field]) && $data[$field] != '') {
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
        $list = $model->page($pageNumber, $pageSize)->order('create_time DESC')->select();
        $cloneModel = new \app\index\model\Blog();
        $total = $cloneModel->options($option)->count();
        return ['list' => $list, 'totalCount' => $total, 'pageSize' => $pageSize, 'pageNo' => $pageNumber];
    }

}