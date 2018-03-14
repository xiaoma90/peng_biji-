<?php
namespace app\backend\controller;

use QiniuUpload\UploadQiniu;

class Qiniu
{
    public function index()
    {
        if ($_FILES['fmvid']['size'] >= 800 * 1024 * 1024) {
            return json(['status' => 0, 'msg' => "你上传的文件过大"]);
        } else {
            $upload = new UploadQiniu();
            $res    = $upload->upload_one($_FILES['fmvid']);
            return json(['status' => 1, 'msg' => $res]);
        }
    }
    public function token()
    {
        $upload = new UploadQiniu();
        return json(['data' => $upload->getToken()]);
    }
    public function tokens()
    {
        $upload = new UploadQiniu();
        return json(['status' => 1, 'msg' => '', 'data' => $upload->getToken()]);
    }

}
