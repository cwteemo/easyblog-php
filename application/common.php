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

if (!function_exists("env")) {
    function env($name, $default = false)
    {
        if (file_exists(ROOT_PATH . '.env.ini')) {
            $array = parse_ini_file(ROOT_PATH . '.env.ini');
            if (!empty($array[$name])) {
                return $array[$name];
            }
        }
        return $default;
    }
}

// 输出自动换行
function secho($str)
{
    echo $str . chr(10) . chr(13);

}

/**
 * 忽略指定字段
 * @param $obj
 * @param $ignoreFields
 */
function unsetFields(&$obj, $ignoreFields)
{
    foreach ($ignoreFields as $field) {
        unset($obj[$field]);
    }
}

/**
 * 删除数组指定value
 * @param $arr
 * @param $value
 * @return array
 */
function delByValue(&$arr, $value)
{
    if (!is_array($arr)) {
        return $arr;
    }
    foreach ($arr as $k => $v) {
        if ($v == $value) {
            unset($arr[$k]);
        }
    }
    return $arr;
}


/**
 * 求 md5 （所有数字转字符串）
 * @param $data
 * @return string
 */

function smd5($data)
{
    if (is_string($data)) {
        return md5($data);
    }
    ksort($data);
    foreach ($data as &$item) {
        if (is_array($item)) {
            $item = smd5($item);
        } else {
            $item = strval($item);
        }
    }
    $str = json_encode($data);
    return md5($str);
}

/**
 * 拼接字符串
 * @param $item
 * @param $fields : 兼容数组和字符串
 * @return string
 */
function joinWithField($item, $fields, $delimiter = "_")
{
    $key = ""; // 拼接 fields 对应的值
    if (is_array($fields)) {
        foreach ($fields as $field) {
            $key .= $delimiter . $item[$field];
        }
    } else {
        $key = $item[$fields];
    }

    return $key;
}


/**
 * 根据 key 去重，并且将重复的 arrKey 合并在数组中
 * @param $visit
 * @param $key
 * @param $arrKey
 * @return array
 */
function deDuplication($visit, $key, $arrKey)
{
    $res = [];
    foreach ($visit as $v) {
        $val = $v[$key];
        $arrVal = $v[$arrKey];

        if (empty($res[$val])) {
            // $key 唯一
            $res[$val] = method_exists($v, 'getData') ? $v->getData() : $v;
            if (!is_array($res[$val][$arrKey])) {
                $res[$val][$arrKey] = [];
            }
        }
        // 存在有效值
        if (!empty($arrVal)) {
            $res[$val][$arrKey][] = $arrVal;
        }

    }
    return $res;
}

/**
 * 某（些）字段分组
 * @param $visit
 * @param $key
 * @return array
 */
function groupVisitWith($visit, $key)
{
    // key 为数组，表示组合索引 进行分组
    $is_array = is_array($key);

    $visit_list = [];
    foreach ($visit as $v) {
        if ($is_array) {
            $val = joinWithField($v, $key);
        } else {
            $val = $v[$key];
        }
        $visit_list[$val][] = $v;
    }
    return $visit_list;
}

/**
 * 联表数据进行分组
 * @param $data
 * @param $append_key
 * @param $key
 * @param $fields
 * @return array
 */
function groupJoinSelectData($data, $append_key, $fields, $key = 'id')
{

    $list = [];
    $data = groupVisitWith($data, $key);
    foreach ($data as $arr) {
        $new_item = [];
        foreach ($arr as $item) {
            $item = method_exists($item, 'getData') ? $item->getData() : $item;
            $group = [];
            // new_item 为空时才做赋值操作
            if (empty($new_item)) {
                foreach ($item as $k => $v) {
                    $new_item[$k] = $v;
                }
            }
            foreach ($fields as $fromKey => $toKey) {
                if (!is_string($fromKey)) {
                    // key不是字符窜，则 fromKey 复用 toKey
                    $fromKey = $toKey;
                }
                if (!empty($item[$fromKey])) {
                    // 如果 fields 是一维数组，则 group 也为一维数组
                    if (count($fields) == 1) {
                        $group[] = $item[$fromKey];
                    } else {
                        $group[$toKey] = $item[$fromKey];
                    }
                }
                // 删除已用来分组的字段
                unset($new_item[$fromKey]);
            }

            if ($new_item['id'] == 15) {
                print_r($item);
            }

            if (!empty($group)) {
                $new_item[$append_key][] = $group;
            }
        }

        // 如果 $new_item 不存在 $append_key，则赋值空数组
        if (!isset($new_item[$append_key])) {
            $new_item[$append_key] = [];
        }
        $list[] = $new_item;
    }
    return $list;
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array|string $child_sort 子节点排序规则（'key' or ['key' => 'key1', 'order' => 'asc']）
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $child_sort = null, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
//        foreach ($list as $key => $data) {
//            $refer[$data[$pk]] = &$list[$key];
//            $refer[$data[$pk]][$child] = [];
//        }
//        foreach ($list as $key => $data) {
//            // 判断是否存在parent
//            $parentId = $data[$pid];
//            if ($root == $parentId) {
//                $tree[] = &$list[$key];
//            } else {
//                if (isset($refer[$parentId])) {
//                    $parent = &$refer[$parentId];
//                    $parent[$child][] = &$list[$key];
//                }
//            }
//        }

        foreach ($list as &$data) {
            $refer[$data[$pk]] = &$data;
            $refer[$data[$pk]][$child] = [];
        }
        foreach ($list as &$data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$data;
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$data;
                }
            }
        }

        // 需要给子元素排序
        if ($child_sort) {
            $sort_key = $child_sort;
            $order = 'desc';
            if (is_array($child_sort)) {
                $sort_key = $child_sort['key'];
                $order = $child_sort['order'];
            }
            foreach ($list as &$data) {
                $data[$child] = list_sort_by($data[$child], $sort_key, $order);
            }
        }

    }

    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param array $tree 原来的树
 * @param string $child 孩子节点的键
 * @param string $order 排序显示的键，一般是主键 升序排列
 * @param array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = 'children', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}


/* 获取数据集中某个ID的family tree
 * [getFamilyTree description]
 * @param  [array] $list [要过滤的数据集]
 * @param  [int] $id  [查询的ID]
 * @return [type]      [匹配的数据]
 */
function get_family_tree($list, $id = 0, $pk = 'id', $pid = 'pid')
{
    $tree = array();
    foreach ($list as $v) {
        if ($v[$pk] == $id) {
            if ($v[$pid] > 0) {
                $tree = array_merge($tree, get_family_tree($list, $v[$pid]));
            }
            $tree[] = $v;
        }
    }
    return $tree;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }

        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }
    return false;
}

function get_tree($data, $pid = 0, $symbol = ' ——')
{
    static $arr = [];
    foreach ($data as $k => $v) {
        if ($v['pid'] == $pid) {
            if ($v['pid'] != 0) {
                $v['name'] = $symbol . $v['name'];
            }
            $arr[] = $v;
            get_tree($data, $v['id']);
        }
    }
    return $arr;
}

function getFirstCharters($zh)
{
    $ret = "";
    $s1 = iconv("UTF-8", "gb2312", $zh);
    $s2 = iconv("gb2312", "UTF-8", $s1);
    if ($s2 == $zh) {
        $zh = $s1;
    }
    for ($i = 0; $i < strlen($zh); $i++) {
        $s1 = substr($zh, $i, 1);
        $p = ord($s1);
        if ($p > 160) {
            $s2 = substr($zh, $i++, 2);
            $ret .= getFirstCharter($s2);
        } else {
            $ret .= $s1;
        }
    }
    return $ret;
}

function getFirstCharter($s0)
{
    $fchar = ord($s0{0});
    if ($fchar >= ord("A") and $fchar <= ord("z")) return strtoupper($s0{0});
    $s1 = $s0;
    $s2 = $s1;
    //$s1 = iconv("UTF-8","gb2312", $s0);
    //$s2 = iconv("gb2312","UTF-8", $s1);
    if ($s2 == $s0) {
        $s = $s1;
    } else {
        $s = $s0;
    }
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 and $asc <= -20284) return "A";
    if ($asc >= -20283 and $asc <= -19776) return "B";
    if ($asc >= -19775 and $asc <= -19219) return "C";
    if ($asc >= -19218 and $asc <= -18711) return "D";
    if ($asc >= -18710 and $asc <= -18527) return "E";
    if ($asc >= -18526 and $asc <= -18240) return "F";
    if ($asc >= -18239 and $asc <= -17923) return "G";
    if ($asc >= -17922 and $asc <= -17418) return "I";
    if ($asc >= -17417 and $asc <= -16475) return "J";
    if ($asc >= -16474 and $asc <= -16213) return "K";
    if ($asc >= -16212 and $asc <= -15641) return "L";
    if ($asc >= -15640 and $asc <= -15166) return "M";
    if ($asc >= -15165 and $asc <= -14923) return "N";
    if ($asc >= -14922 and $asc <= -14915) return "O";
    if ($asc >= -14914 and $asc <= -14631) return "P";
    if ($asc >= -14630 and $asc <= -14150) return "Q";
    if ($asc >= -14149 and $asc <= -14091) return "R";
    if ($asc >= -14090 and $asc <= -13319) return "S";
    if ($asc >= -13318 and $asc <= -12839) return "T";
    if ($asc >= -12838 and $asc <= -12557) return "W";
    if ($asc >= -12556 and $asc <= -11848) return "X";
    if ($asc >= -11847 and $asc <= -11056) return "Y";
    if ($asc >= -11055 and $asc <= -10247) return "Z";
    return null;
}

/**
 * 对比新老数据，获取需要新增和删除的数据
 * @param $new
 * @param $old
 * @param null $keys (预留兼容二维数据)
 * @return array
 */
function s_array_diff($new, $old, $keys = null)
{
    if (empty($old)) {
        return [
            $new, null
        ];
    }

    // 新老数组共同元素（交集）
    $common = array_intersect($new, $old);

    return [
        // 新数据差集
        array_diff($new, $common),
        // 老数据（如果以$new为准，可以删除 old 的部分）
        // 老数据差集
        array_diff($old, $common)
    ];
}


/**
 * 将一维数组合并成二维数组 or 二维数组 $array1 每一项都合并 $array2
 * eg:
 *  输入：$array1 = [1,2,3]; $array2 = [ 'a' => 3, 'b' => 1 ]; $key1 = 'c';
 *  输出：[ [ 'c' => 1, 'a' => 3, 'b' => 1 ], [ 'c' => 2, 'a' => 3, 'b' => 1 ], [ 'c' => 3, 'a' => 3, 'b' => 1 ], ]
 *
 *  输入：$array1 = [['c' => 1], ['c' => 2], ['c' => 3]]; $array2 = [ 'a' => 3, 'b' => 1 ]; $key1 = null;
 *  输出：[ [ 'c' => 1, 'a' => 3, 'b' => 1 ], [ 'c' => 2, 'a' => 3, 'b' => 1 ], [ 'c' => 3, 'a' => 3, 'b' => 1 ], ]
 *
 * @param $array1
 * @param $array2
 * @param null $key1
 * @return array
 */
function s_array_merge($array1, $array2, $key1 = null)
{
    $res = [];
    foreach ($array1 as $val) {
        if ($key1) {
            $item = [
                $key1 => $val
            ];
        } else {
            // key1 不存在表示 $array1 为二维数组
            $item = $val;
        }
        $where_in = [];
        foreach ($array2 as $key => $val2) {
            if (!is_array($val2)) {
                $item[$key] = $val2;
            } else {
                $where_in[$key] = $val2[0] == 'in' ? $val2[1] : null;
            }
        }
        if (!empty($where_in)) {
            foreach ($where_in as $k => $value) {
                foreach ($value as $v) {
                    $item[$k] = $v;
                    array_push($res, $item);
                }
            }
        } else {
            array_push($res, $item);
        }

    }

    return $res;
}

/**
 * 高效数组去重
 * @param $arr
 * @param $is_str
 * @return array
 */
function s_array_unique($arr, $is_str = true)
{
    $arr = array_filter($arr);
    $arr = array_flip($arr);
    $arr = array_keys($arr);

    if ($is_str) {
        $arr = array_map('strval', $arr);
    }

    return $arr;
}

/**
 * 判断字符串是否被包含在target中
 * @param $str
 * @param $target
 * @return bool
 */
function checkStr($str, $target)
{
    if (preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", $str) == preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", "", $target)) {
        return true;
    }
    return false;
}

/**
 * 替换字符串限制次数
 * @param $search
 * @param $replace
 * @param $subject
 * @param int $limit
 * @return string|string[]|null
 */
function str_replace_limit($search, $replace, $subject, $limit = -1)
{
    // constructing mask(s)...
    if (is_array($search)) {
        foreach ($search as $k => $v) {
            $search[$k] = '`' . preg_quote($search[$k], '`') . '`';
        }
    } else {
        $search = '`' . preg_quote($search, '`') . '`';
    }
    // replacement
    return preg_replace($search, $replace, $subject, $limit);
}

/**
 * 自定义排序  升序
 */
function xsort(&$sort_array, $sort_key_name)
{

    for ($i = 0; $i < count($sort_array); $i++) {

        for ($j = $i + 1; $j < count($sort_array); $j++) {

            if ($sort_array[$i][$sort_key_name] < $sort_array[$j][$sort_key_name]) {

                $temp = $sort_array[$i];

                $sort_array[$i] = $sort_array[$j];

                $sort_array[$j] = $temp;
            }
        }
    }
}

/**
 * 获取本机ip
 * @return string
 */
function get_local_ip()
{

//    $server_hostname = gethostname();
//    $server_hostname .= ".";
//    $host_ip = gethostbyname($server_hostname);

//    $host_name = exec("hostname");
//    $host_ip = gethostbyname($host_name); //获取本机的局域网IP


    $path = DS . ROOT_DIR_NAME . '/public/index.php' . DS;
    $host_ip = env('ip', '152.28.83.101');
    if (!$host_ip) {
        // TODO 昆明公安

        $host_ip = '152.28.83.101';
        //拉薩
//    $host_ip = '192.168.1.200';
        // TODO 检察院测试机
//    $host_ip = '10.113.10.67';
        // TODO 人大
//    $host_ip = '172.17.45.76';
//    $host_ip = '39.97.165.161';

//    $path = DS  . 'server' . DS;
    }
    $port = env('port', '80');
    return $path;
}


/**
 * 获取客户端真实ip
 * @return bool|string
 */
function get_real_ip()
{
    $ip = '';

    //客户端IP 或 NONE
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }

    //多重代理服务器下的客户端真实IP地址（可能伪造）,如果没有使用代理，此字段为空
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = FALSE;
        }
        for ($i = 0; $i < count($ips); $i++) {
            if (!eregi("^(10│172.16│192.168).", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }

    //客户端IP 或 (最后一个)代理服务器 IP
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

/**
 * @param string $url post请求地址
 * @param array $params
 * @return mixed
 */
function curl_post($url, array $params = array(), $contentType = 'Content-Type: application/json')
{
    if ($contentType == 'Content-Type: application/json') {
        $data_string = json_encode($params, JSON_UNESCAPED_UNICODE);
    } else {
        $data_string = $params;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    curl_setopt(
        $ch, CURLOPT_HTTPHEADER,
        array(
            $contentType
        )
    );
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}

function curl_post_raw($url, $rawData)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);

    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}

/**
 * @param string $url get请求地址
 * @param int $httpCode 返回状态码
 * @return mixed
 */
function curl_get($url, &$httpCode = 0)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //不做证书校验,部署在linux环境下请改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
}


/**
 * 返回随机字符串
 * @param int $length
 * @return  string
 */
function getRandChar($length)
{

    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }

    return $str;
}

/**
 * @param [num] $num [数字]
 * @return [string] [string]
 * @author ja颂
 * 把数字1-1亿换成汉字表述，如：123->一百二十三
 */
function numberToCH($num)
{

    $chiNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
    $chiUni = array('', '十', '百', '千', '万', '亿', '十', '百', '千');

    $chiStr = '';

    $num_str = (string)$num;

    $count = strlen($num_str);
    $last_flag = true; //上一个 是否为0
    $zero_flag = true; //是否第一个
    $temp_num = null; //临时数字

    $chiStr = '';//拼接结果
    if ($count == 2) {//两位数
        $temp_num = $num_str[0];
        $chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num] . $chiUni[1];
        $temp_num = $num_str[1];
        $chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
    } else if ($count > 2) {
        $index = 0;
        for ($i = $count - 1; $i >= 0; $i--) {
            $temp_num = $num_str[$i];
            if ($temp_num == 0) {
                if (!$zero_flag && !$last_flag) {
                    $chiStr = $chiNum[$temp_num] . $chiStr;
                    $last_flag = true;
                }
            } else {
                $chiStr = $chiNum[$temp_num] . $chiUni[$index % 9] . $chiStr;

                $zero_flag = false;
                $last_flag = false;
            }
            $index++;
        }
    } else {

        $chiStr = $chiNum[$num_str[0]];

    }

    return $chiStr;

}

//壹拾贰亿叁仟肆佰伍拾陆万柒仟捌佰玖拾圆
//先贴一个数字转中文的，最多12位数
function convert_2_cn($num) {
    $convert_cn = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
    $repair_number = array('零仟零佰零拾零','万万','零仟','零佰','零拾');
    $unit_cn = array("拾","佰","仟","万","亿");
    $exp_cn = array("","万","亿");
    $max_len = 12;

    $len = strlen($num);
    if($len > $max_len) {
        return 'outnumber';
    }
    $num = str_pad($num,12,'-',STR_PAD_LEFT);
    $exp_num = array();
    $k = 0;
    for($i=12;$i>0;$i--){
        if($i%4 == 0) {
            $k++;
        }
        $exp_num[$k][] = substr($num,$i-1,1);
    }
    $str = '';
    foreach($exp_num as $key=>$nums) {
        if(array_sum($nums)){
            $str = array_shift($exp_cn) . $str;
        }
        foreach($nums as $nk=>$nv) {
            if($nv == '-'){continue;}
            if($nk == 0) {
                $str = $convert_cn[$nv] . $str;
            } else {
                $str = $convert_cn[$nv].$unit_cn[$nk-1] . $str;
            }
        }
    }
    $str = str_replace($repair_number,array('万','亿','-'),$str);
    $str = preg_replace("/-{2,}/","",$str);
    $str = str_replace(array('零','-'),array('','零'),$str);
    if (!$str){
        $str = '零';
    }
    return $str;
}