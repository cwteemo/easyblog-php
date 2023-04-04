<?php


namespace app\index\validate;


use app\index\lib\Res;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {

        //获取Http传入的参数
        //对参数做校验
        $request = Request::instance();

        $params = $request->param();

        $result = $this->batch()->check($params);

        if (!$result) {

            $error = $this->error;

            $msg = '未知错误';
            foreach ($error as $text) {
                $msg = $text;
                break;
            }
            Res::returnErr($msg);

        } else {

            return true;
        }

    }


    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {

        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {

            return true;

        } else {

            return false;

        }

    }

    protected function isIntegerGreaterZero($value, $rule = '', $data = '', $field = '')
    {

        if (is_numeric($value) && is_int($value + 0) && ($value + 0) >= 0) {

            return true;

        } else {

            return false;

        }

    }

    protected function isNotEmpty($value, $rule = '', $data = '', $field = '')
    {
        if (empty($value)) {
            return $field . '不允许为空';
        } else {
            return true;
        }
    }


    /**
     * @param array $params 通常传入request.post变量数组
     * @return array 按照规则key过滤后的变量数组
     * @throws ParameterException
     */
    public function getParamsByRule($params)
    {

//        if (array_key_exists('user_id', $params)) {
//
//            // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
//            throw new ParameterException([
//                'code' => 200,
//                'msg' => '参数中包含有非法的参数名user_id或者uid'
//            ]);
//        }

        $params_data = [];

        foreach ($this->rule as $key => $value) {

            if (array_key_exists($key, $params)) {

                $params_data[$key] = $params[$key];

            }

        }

        return $params_data;
    }


}