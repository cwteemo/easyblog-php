<?php

namespace app\index\lib;
use think\Exception;
use think\response\Json;

/**
 * Class Res
 * @package app\index\lib
 *
 */
class Res extends Exception
{
    public $msg;

    /**
     * 10001 为登录失效
     * @var int
     */
    public $errorCode;

    public $data;

    public function __construct($data = [], $msg = '', $errorCode = 0)
    {
        $this->msg = $msg;

        $this->errorCode = $errorCode;

        $this->data = $data;

    }

//    public static function reLogin ()
//    {
//        return new Res(null, '',10001);
//    }

    /**
     * 返回错误
     * @param string $msg
     * @param int $errorCode
     * @return Res
     */
    public static function error($msg="", $errorCode=1)
    {
        return new Res([], $msg, $errorCode);
    }

    /**
     * 重新登录
     */
    public static function reLogin()
    {
        static::returnErr("登录状态已失效",10001);
    }

    /**
     * 终止程序，返回错误
     * @param int $errorCode
     * @param string $msg
     */
    public static function returnErr($msg="", $errorCode=1)
    {
        header('Content-Type: application/json; charset=utf-8');
        abort(Json::create(Res::error($msg, $errorCode)));
    }

}