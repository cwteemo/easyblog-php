<?php
/**
 * Created by PhpStorm.
 * User: sMac
 * Date: 2021-05-07
 * Time: 11:48
 */

namespace app\behavior;


use app\index\lib\Res;
use think\Response;

class AppBehavior
{

    public function appBegin($dispatch)
    {

    }

    public function responseSend(Response $response)
    {
        $action = request()->action();
        if (!in_array($action, filter_action())) {
            $res = $response->getData();
            if (!($res instanceof Res)) {
                // 不是 Res 的实例就转换为 Res 实例
                if (is_string($res) && $res != strip_tags($res)) {
                    print_r($res);
                    die();
                }
                $res = new Res(isset($res) ? $res : []);
            }

            // 回调行为
            if ($response->getCode() != 200) {
                $response->code(200);
                if (!empty($res->data['message'])) {
                    $res->msg = $res->data['message'];
                }
                if ($res->errorCode == 0) {
                    $res->errorCode = 1;
                }
            } else {
            }
            $response->data($res);
        }
        // $response->contentType('application/json');
        return $response;
    }

    public function responseEnd(Response $response)
    {
    }
}