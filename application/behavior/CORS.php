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
        if (request()->isOptions()) {
            exit();
        }
    }

}