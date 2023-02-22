<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
define("ROOT_DIR_NAME", 'easyblog');
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // windows 服务器
    $data_path = null;
    $rootdata_path = ROOT_PATH . "stand-anlone.bash_profile";
    if (file_exists($rootdata_path)) {
        $file_contents = file($rootdata_path);
        foreach ($file_contents as $file_content) {
            $lien_arr = explode('=', $file_content);
            if (count($lien_arr) >= 2) {
                if ($lien_arr[0] == 'DATA_PATH') {
                    $data_path = trim($lien_arr[1]);
                    break;
                }
            }
        }
    }
    // 软件系统环境变量有配置路径就用环境变量的路径
    if ($data_path && is_dir($data_path)) {
        define('DATA_PATH', $data_path . DS . ROOT_DIR_NAME . DS);
        define('DATA_PATH_BAK', $data_path . DS . 'uploads_bak' . DS . ROOT_DIR_NAME . DS);

    } else {
        define('DATA_PATH', 'c:/data' . DS . ROOT_DIR_NAME . DS);
        define('DATA_PATH_BAK', 'c:/uploads_bak' . DS . ROOT_DIR_NAME . DS);
    }
    define('PHP_DIR_DS', '\\');
} else {
    define('PHP_DIR_DS', '/');
    define('DATA_PATH', '/data' . DS . ROOT_DIR_NAME . DS);
    define('DATA_PATH_BAK', '/data/uploads_bak' . DS . ROOT_DIR_NAME . DS);
}
function filter_action()
{
    return [
        'getcheckcode'
    ];
}

function postParams()
{
    return input('post.');
}

function getParams()
{
    return input('get.');
}

function set_log($filename, $str)
{
    file_put_contents($filename, $str, FILE_APPEND);
}