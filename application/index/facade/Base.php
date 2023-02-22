<?php


namespace app\index\facade;


class Base
{

    /**
     * @param $fields
     * @param $array
     * @return array
     * 生成指定key的数组
     */
    public function initFixedKeyArray($fields, $array)
    {
        $new_arr = [];
        foreach ($fields as $field) {
            $new_arr[$field] = isset($array[$field]) ? $array[$field] : null;
        }
        return $new_arr;
    }

    /**
     * @param $mapping
     * @param $array
     * @return array
     * 修改数组的key，并返回新的key对应的数据
     */
    public function filterArray($mapping, $array)
    {
        $new_arr = [];
        foreach ($mapping as $old_field => $new_field) {
            if (is_numeric($old_field)) {
                $old_field = $new_field;
            }
            $new_arr[$new_field] = isset($array[$old_field]) ? $array[$old_field] : null;
        }
        return $new_arr;
    }
}