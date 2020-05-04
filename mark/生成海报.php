<?php
/**
 * 生成海报
 * 依赖 gd 库
 */
function createPoster()
{
    /**
     * 背景图
     * 商品主图
     * 商品标题
     * 头像
     * 昵称
     * 二维码
     * 长按立即购买
     * 抢购价图片
     * 抢购价
     * 原价
     * 分区
     */
    $lon = trim($this->request->param('lon'));
    $lat = trim($this->request->param('lat'));
//    $m = new Map();
    $area = '';
//    if (Config::get('site.is_location_video_data')) {
//        if ($lon && $lat) {
//            $location = $m->getAddress1($lon, $lat);
//            // 查询分页数据
//            if ($location && $location['status'] == 0) {
//                $area = $location['result']['addressComponent']['district'];
//            }
//        }
//    }
    $area .= '淇淇果';
    $goods_id = $this->request->param('goods_id/d',0);
    $goods = Db::name('goods')->where(['id'=>$goods_id])->find();
    $goods_size = Db::name('goods_size')->where('gid',$goods_id)->find();
//        $goods_image = ROOT_PATH .$goods['img1'];
    $goods_image = 'http://cdnvideo.qiqiguo.cn'.$goods['img1'].'?x-oss-process=image/resize,m_lfit,h_628,w_628';
    $goods_title = $goods['name'];
    $user = Db::name('user')->where('id',$this->uid)->find();
    $avatar = 'http://cdnvideo.qiqiguo.cn'.$user['headimg'].'?x-oss-process=image/resize,m_lfit,h_90,w_90';
    $nickname = $user['nick'];
    vendor("QRcode.phpqrcode");
    // 根据商品ID生成二维码图
    $PNG_TEMP_DIR = 'uploads/poster/';
    if (!file_exists($PNG_TEMP_DIR)){
        mkdir($PNG_TEMP_DIR, 0777, true);  // 开启权限
    }
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 3;   // 点大小
    $code = $PNG_TEMP_DIR.'goods_code_'.$goods_id.'.png';
    $qrcode = new \QRcode();
    $link = 'http://wap.qiqiguo.cn/mall/shangpin_detail.html?id='.$goods_id;
    $qrcode->png($link, $code, $errorCorrectionLevel, $matrixPointSize, 2);

    $config = array(
        'text'=>array(
            array(
                'text'=>$goods_title,
                'left'=>75,
                'top'=>825,
//                    'right'=>75,
//                    'bottom'=>417,
                'fontPath'=>'uploads/poster/simhei.ttf',     //字体文件
                'fontSize'=>17,             //字号
                'fontColor'=>'51,51,51',       //字体颜色
                'angle'=>0,
            ),array(
                'text'=>$nickname,
                'left'=>189,
                'top'=>1062,
//                    'bottom'=>267,
                'fontPath'=>'uploads/poster/simhei.ttf',     //字体文件
                'fontSize'=>24,             //字号
                'fontColor'=>'51,51,51',       //字体颜色
                'angle'=>0,
            ),array(
                'text'=>$area,
                'left'=>245,
                'top'=>1260,
//                    'bottom'=>44,
                'fontPath'=>'uploads/poster/simhei.ttf',     //字体文件
                'fontSize'=>32,             //字号
                'fontColor'=>'51,51,51',       //字体颜色
                'angle'=>0,
            ),array(
                'text'=>'长按立即购买',
                'left'=>531,
                'top'=>1141,
//                    'bottom'=>160,
//                    'right'=>75,
                'fontPath'=>'uploads/poster/simhei.ttf',     //字体文件
                'fontSize'=>17,             //字号
                'fontColor'=>'153,153,153',       //字体颜色
                'angle'=>0,
            ),array(
                'text'=>'￥'.$goods_size['xprice'],
                'left'=>183,
                'top'=>1140,
//                    'bottom'=>165,
                'fontPath'=>'uploads/poster/simhei.ttf',     //字体文件
                'fontSize'=>28,             //字号
                'fontColor'=>'244,85,82',       //字体颜色
                'angle'=>0,
            ),array(
                'text'=>'￥'.$goods_size['yprice'],
                'left'=>348,
                'top'=>1150,
//                    'bottom'=>174,
                'fontPath'=>'uploads/poster/simhei.ttf',     //字体文件
                'fontSize'=>18,             //字号
                'fontColor'=>'191,191,191',       //字体颜色
                'angle'=>0,
            ),array(
                'text'=>'--------',
                'left'=>348,
                'top'=>1150,
//                    'bottom'=>174,
                'fontPath'=>'uploads/poster/simhei.ttf',     //字体文件
                'fontSize'=>18,             //字号
                'fontColor'=>'191,191,191',       //字体颜色
                'angle'=>0,
            )
        ),
        'image'=>array(
            array(
                'url'=>$goods_image,       //图片资源路径
                'left'=>61,
                'top'=>173,
                'stream'=>0,             //图片资源是否是字符串图像流
                'right'=>0,
                'bottom'=>0,
                'width'=>628,
                'height'=>628,
                'opacity'=>100
            ),array(
                'url'=>'uploads/poster/bai.png',
                'left'=>45,
                'top'=>965,
                'right'=>0,
                'stream'=>0,
                'bottom'=>0,
                'width'=>660,
                'height'=>239,
                'angle'=>1,
                'opacity'=>100
            ),array(
                'url'=>$avatar,
                'left'=>75,
                'top'=>999,
                'right'=>0,
                'stream'=>0,
                'bottom'=>0,
                'width'=>90,
                'height'=>90,
                'angle'=>0.5,
                'opacity'=>100
            ),array(
                'url'=>'uploads/poster/panic.png',
                'left'=>75,
                'top'=>1120,
                'right'=>0,
                'stream'=>0,
                'bottom'=>0,
                'width'=>92,
                'height'=>42,
                'opacity'=>100
            ),array(
                'url'=>$code,
                'left'=>538,
                'top'=>995,
                'right'=>0,
                'stream'=>0,
                'bottom'=>0,
                'width'=>130,
                'height'=>130,
                'opacity'=>100
            )
        ),
        'background'=>'uploads/poster/background.png',
    );

    $filename = $PNG_TEMP_DIR.'goods_'.$goods_id.'.png';;

    //如果要看报什么错，可以先注释调这个header
    if(empty($filename)) header("content-type: image/png");
    $imageDefault = array(
        'left'=>0,
        'top'=>0,
        'right'=>0,
        'bottom'=>0,
        'width'=>100,
        'height'=>100,
        'opacity'=>100
    );
    $textDefault = array(
        'text'=>'',
        'left'=>0,
        'top'=>0,
        'fontSize'=>32,       //字号
        'fontColor'=>'255,255,255', //字体颜色
        'angle'=>0,
    );
    $background = $config['background'];//海报最底层得背景
    //背景方法
    $backgroundInfo = getimagesize($background);
    $backgroundFun = 'imagecreatefrom'.image_type_to_extension($backgroundInfo[2], false);
    $background = $backgroundFun($background);
    $backgroundWidth = imagesx($background);  //背景宽度
    $backgroundHeight = imagesy($background);  //背景高度
    $imageRes = imageCreatetruecolor($backgroundWidth,$backgroundHeight);
    $color = imagecolorallocate($imageRes, 255, 255, 255);//背景色
    imagefill($imageRes, 0, 0, $color);
    // imageColorTransparent($imageRes, $color);  //颜色透明
    imagecopyresampled($imageRes,$background,0,0,0,0,imagesx($background),imagesy($background),imagesx($background),imagesy($background));
    //处理了图片
    if(!empty($config['image'])){
        foreach ($config['image'] as $key => $val) {
            $val = array_merge($imageDefault,$val);
            $info = getimagesize($val['url']);
            $function = 'imagecreatefrom'.image_type_to_extension($info[2], false);
            if($val['stream']){   //如果传的是字符串图像流
                $info = getimagesizefromstring($val['url']);
                $function = 'imagecreatefromstring';
            }
            $res = $function($val['url']);
            $resWidth = $info[0];
            $resHeight = $info[1];
            //建立画板 ，缩放图片至指定尺寸
            $canvas=imagecreatetruecolor($val['width'], $val['height']);
            imagefill($canvas, 0, 0, $color);
            //关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
            imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'],$resWidth,$resHeight);
            $val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']) - $val['width']:$val['left'];
            $val['top'] = $val['top']<0?$backgroundHeight- abs($val['top']) - $val['height']:$val['top'];
            //放置图像
            imagecopymerge($imageRes,$canvas, $val['left'],$val['top'],$val['right'],$val['bottom'],$val['width'],$val['height'],$val['opacity']);//左，上，右，下，宽度，高度，透明度
        }
    }
    //处理文字
    if(!empty($config['text'])){
        foreach ($config['text'] as $key => $val) {
            $val = array_merge($textDefault,$val);
            list($R,$G,$B) = explode(',', $val['fontColor']);
            $fontColor = imagecolorallocate($imageRes, $R, $G, $B);
            $val['left'] = $val['left']<0?$backgroundWidth- abs($val['left']):$val['left'];
            $val['top'] = $val['top']<0?$backgroundHeight- abs($val['top']):$val['top'];
            imagettftext($imageRes,$val['fontSize'],$val['angle'],$val['left'],$val['top'],$fontColor,$val['fontPath'],$val['text']);
        }
    }
    //生成图片
    if(!empty($filename)){
        $res = imagejpeg ($imageRes,$filename,90); //保存到本地
        imagedestroy($imageRes);
        if(!$res) $this->result('', 0, '网络错误，请稍后重试');
        $this->result('http://www.qiqiguo.cn'.$filename, 1, 'ok');
    }else{
        imagejpeg ($imageRes);     //在浏览器上显示
        imagedestroy($imageRes);
    }

}