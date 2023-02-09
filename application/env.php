<?php
/*
 * 获取配置信息
 */
function env($name,$default = false)
{
    if (file_exists(ROOT_PATH . '.env.ini')){
        $array = parse_ini_file(ROOT_PATH . '.env.ini');
        if (!empty($array[$name])) {
            return $array[$name];
        }
    }
    return $default;
}