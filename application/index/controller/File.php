<?php


namespace app\index\controller;


use app\index\lib\Res;
use app\index\lib\SnowFlake;
use think\Db;
use think\Exception;

class File extends Base
{
    public function postUpload()
    {
        try {
            $file = request()->file('file');
            if ($file) {
                $path = "uploads";
                $info = $file->move(DATA_PATH . $path, SnowFlake::generateParticle());
                if ($info) {
                    $filename = $path . '/' . $info->getFilename();
                    Db::startTrans();
                    $res = \app\index\model\File::create(['path' => $filename, 'suffix' => $info->getExtension()]);
                    Db::commit();
                    return $res;
                } else {
                    // 上传失败获取错误信息
                    throw new Exception($file->getError());
                }
            }else{
                throw new Exception('文件不存在');
            }
        } catch (Exception $exception) {
            Db::rollback();
            Res::returnErr($exception->getMessage());
        }
    }

    public function getUrl(){
        $params = getParams();
        $path = \app\index\facade\File::getRootDataPath(urldecode($params['name']));
        header("Cache-Control: public");
        header("Pragma: cache");
        $offset = 30 * 60 * 60 * 24; // cache 1 month
        $ExpStr = "Expires: " . gmdate("D, d M Y H:1:s", time() + $offset) . " GMT";
        header($ExpStr);
        if (isset($params['preview'])) {
            // todo: 去掉 previewdel 这个重复意义的字段，keep这个字段本来就有是否保留的意思，keep可以分别和 preview 和 download 组合使用
            // 预览文件
            \app\index\facade\File::buildPreviewData($path, isset($params['previewdel']) || isset($params['file_del']));

        } else if (isset($params['download'])) {

            // 下载文件
            \app\index\facade\File::buildDownloadData($path, !isset($params['keep']));

        } else {
            // 返回图片
            \app\index\facade\File::buildImageData($path, $params);
        }

    }

}