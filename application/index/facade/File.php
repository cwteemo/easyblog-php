<?php


namespace app\index\facade;


class File extends Base
{
    /**
     * 获取文件绝对路径（兼容绝对路径 和 相对路径）
     * @param $path
     * @return string
     */
    static public function getRootDataPath($path)
    {
        // 如果 name 不是绝对目录，则拼接绝对目录
        if (strpos($path, DATA_PATH) !== 0) {
            $path = DATA_PATH . $path;
        }
        return $path;
    }

    /**
     * 构建输出预览数据
     * @param $path
     * @param $del
     */
    public static function buildPreviewData($path, $del)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $path);
        finfo_close($finfo);

        header('Content-Type: ' . $mimetype);
        header('Content-Length: ' . filesize($path));
        ob_end_clean();
        flush();
        @readfile($path);
        if ($del) {
            unlink($path);
        }
    }

    /**
     * 构建输出下载数据
     * @param $path
     * @param $del
     */
    public static function buildDownloadData($path, $del)
    {
        if (is_file($path)) {
            $file_size = filesize($path);
            $file = fopen($path, 'rb');
            header("Content-Type: application/octet-stream;charset=utf-8;");
            header("Accept-Ranges: bytes");
            header("Accept-Length: " . $file_size);

            header("Content-Disposition: attachment; filename=" . substr(strrchr($path, '/'), 1));
            header("Content-Disposition: attachment; filename=" . basename($path));
            ob_end_clean();
            flush();

            echo fread($file, $file_size);
            fclose($file);
            if ($del) {
                unlink($path);
                $temp_dir = explode('.', $path)[0];
                exec("rm -rf $temp_dir");
            }
        }
    }

    /**
     * 构建图片数据
     * @param $path
     * @param $params
     * @throws \ImagickException
     */
    public static function buildImageData($path)
    {
        $pathinfo = pathinfo($path);
        $ext = $pathinfo['extension'];
        $img = file_get_contents($path);
        //使用图片头输出浏览器
        header("Content-Type: image/$ext;text/html; charset=utf-8");
        ob_end_clean();
        flush();
        echo $img;
    }
}