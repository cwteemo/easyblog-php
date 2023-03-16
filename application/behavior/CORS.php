<?php

namespace app\behavior;
/**
 * Created by PhpStorm.
 * User: sMac
 * Date: 2/26/19
 * Time: 15:05
 */

use think\Request;
use think\Response;

class CORS
{
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: *');
        if (request()->isOptions()) {
            exit();
        }
    }

}