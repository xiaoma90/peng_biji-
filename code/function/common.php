<?php
// 应用公共文件
use app\backend\model\UsersModel;
use think\Session;

# 判断是否是微信内部浏览器
function isWeixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

# 生成用户唯一标识
function getuserid()
{
    do {
        $output  = '';
        $pattern = '1234567890abcdefghjkmnopqrstuvwyzABCDEFGHJKOMNOPQRSTUVWXYZ';
        for ($a = 0; $a < 4; $a++) {
            $output .= $pattern{mt_rand(0, 30)}; //生成php随机数
        }
        $userId = $output;
    } while (db('users')->where(['userid' => $userId])->count());

    return $userId;
}
/**
 * 生成二维码
 * @param int $id
 * @param $level 容错等级
 * @param $size 图片大小
 * @return
 */
function qrcode($id, $level = 3, $size = 4)
{
    vendor('phpqrcode.phpqrcode');
    $url                  = "./uploads/qrcode/$id.png";
    $errorCorrectionLevel = intval($level); //容错级别
    $matrixPointSize      = intval($size); //生成图片大小
    //生成二维码图片
    $to_url = "http://rj.runjiaby.com/home/wechat/BrowserType?param=&pid=" . $id;
    $object = new \QRcode();
    $img    = $object->png($to_url, $url, $errorCorrectionLevel, $matrixPointSize, 2, false);
}

/**
 * 把数据写入一个文件
 *
 * @param string          $file    文件名
 * @param array|generator $data    数据，可以被 foreach 遍历的数据，数组或者生成器
 * @param string          $tplFile 模板文件，以哪个模板填写数据，如果不提供则生成空白 xlsx 文件
 * @param int             $skipRow 跳过表头的行数，默认为 1
 */
function putExcel($file, $data, $tplFile = null, $skipRow = 1)
{

    Vendor("PHPExcel.PHPExcel");
   
    if ($tplFile) {
        if (file_exists($tplFile)) {
            $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel = $objReader->load($tplFile);
        } else {
            throw new \Exception("File `{$tplFile}` not exists");
        }
    } else {
        $objPHPExcel = new \PHPExcel();
    }
    $objPHPExcel->setActiveSheetIndex(0);
    $objSheet=$objPHPExcel->getActiveSheet();
    $objSheet->setTitle('export');
    $rowNum = 1;
    
    foreach ($data as $row) {
        $colNum = 0;
        foreach ($row as $val) {
            $objSheet->setCellValueByColumnAndRow(
                $colNum,
                $rowNum + $skipRow,
                $val
            );
            ++$colNum;
        }
        ++$rowNum;
    }

    PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007')->save($file);
    
}

/**
 *
 * 导出Excel -- 例子
 * @param $data->二维数组
 */
function put_excel($filename,$data){//导出Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename='.$filename.'.xls');
    header('Cache-Control: max-age=0');
    # 默认跳过第一行表头
    
    putExcel('php://output', $data);
    
    exit();
}

/**
 * 从 Excel 获取所有行
 *
 * @param string   $file          xlsx 文件路径
 * @param int|null $highestColumn 列数，为 null 时候自动检测
 * @param array    $skipRows      跳过的行，默认跳过第一行（表头）
 * @param bool     $skipBlankRow  是否跳过空白行，默认为 true
 *
 * @return generator 可遍历的生成器
 */
function writeExcel($file, $highestColumn = null, $skipRows = [1], $skipBlankRow = true)
{

    vendor("PHPExcel.PHPExcel");

    $objReader = PHPExcel_IOFactory::createReader('Excel2007');

    $objPHPExcel = $objReader->load($file);

    $sheet      = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();

    is_null($highestColumn) and $highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

    for ($row = 1; $row <= $highestRow; ++$row) {

        if (in_array($row, $skipRows)) {

            continue;
        }

        $rowData = [];
        for ($col = 0; $col < $highestColumn; $col++) {

            $value     = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();
            $rowData[] = is_null($value) ? '' : $value;

        }

        if ($skipBlankRow) {

            if (!array_filter($rowData)) {

                continue;
            }
        }

        yield $rowData;
    }

}

/**
 *
 * 导出Excel -- 例子
 * @param $data->二维数组
 */
function putExcel1($filename, $data)
{
//导出Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=' . $filename . '.xls');
    header('Cache-Control: max-age=0');
    # 默认跳过第一行表头

    putExcel('php://output', $data);

    exit();
}

//随机生成唯一订单号
function orderSn()
{
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    return (String) $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . time() . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 999));
}

//令牌
function _token()
{
    $yCode       = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $encrypt_key = md5(((float) date("YmdHis") + rand(100, time())) . rand(1000, 9999) . $yCode[intval(date('Y')) - 2011]);
    $token       = md5(md5(substr(time(), 0, 3) . $encrypt_key));
    return $token;
}

/**
 * 通过CURL发送HTTP请求
 * @param string $url  //请求URL
 * @param array $postFields //请求参数
 * @return mixed
 */
function curlPost($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
    // 'Content-Type: application/json; charset=utf-8'
    // )
    // );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt( $ch, CURLOPT_POST, 1 );
    // curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $ret = curl_exec($ch);
    // if (false == $ret) {
    //     $result = curl_error(  $ch);
    // } else {
    //     $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
    //     if (200 != $rsp) {
    //         $result = "请求状态 ". $rsp . " " . curl_error($ch);
    //     } else {
    //         $result = $ret;
    //     }
    // }
    curl_close($ch);
    return $ret;
}
/**
 *根据某字段对多维数组进行排序
 *@param $array  要排序的数组
 *@param $field  要根据的数组下标
 *@return void
 */
function sortArrByField(&$array, $field, $desc = false)
{
    $fieldArr = array();
    foreach ($array as $k => $v) {
        $fieldArr[$k] = $v[$field];
    }
    $sort = $desc == false ? SORT_ASC : SORT_DESC;
    array_multisort($fieldArr, $sort, $array);
}
//转码
function inputCsv($handle)
{
    $out = array();
    $n   = 0;
    while ($data = fgetcsv($handle, 10000)) {
        $num = count($data);
        for ($i = 0; $i < $num; $i++) {
            $out[$n][$i] = $data[$i];
        }
        $n++;
    }
    return $out;
}
//转换格式
function transformation($kv)
{
//转码
    $encode = mb_detect_encoding($kv, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
    if ($encode != 'UTF-8') {
        $kv = iconv($encode, 'utf-8', $kv);
    }
    return $kv;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function getClientIp($type = 0, $adv = false)
{
    $type      = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * @param $Mobile
 * @return array
 * @auther mapengcheng
 */
function NewSms($Mobile, $type = 1)
{
    db('phone_code')->where('phone', $Mobile)->update(['status' => 2]);
    $str     = "1234567890123456789012345678901234567890";
    $str     = str_shuffle($str);
    $code    = substr($str, 3, 6);
    $data    = "username=%s&password=%s&mobile=%s&content=%s";
    $url     = "http://120.55.248.18/smsSend.do?";
    $name    = "CXSC";
    $pwd     = md5("sT5eP8kI");
    $pass    = md5($name . $pwd);
    $to      = $Mobile;
    $content = "您本次的验证码是" . $code . "，请在10分钟内填写，切勿将验证码泄露于他人。【民创文化】";
    $content = urlencode($content);
    $rdata   = sprintf($data, $name, $pass, $to, $content);
    db('phone_code')->insert(['phone' => $to, 'code' => $code, 'type' => $type, 'created_at' => date('YmdHis')]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rdata);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return ['code' => $code, 'data' => $result, 'msg' => ''];
}

#发送验证码
function code($phone)
{

    //短信接口用户名 $uid
    $uid = 'WHJS002183';
    //短信接口密码 $passwd
    $passwd = 'runjia@123';
    //发送到的目标手机号码 $telphone
    $telphone = $phone;
    $num      = rand('100000', '999999');
    //$num=111111;
    $data          = [];
    $data['code']  = $num;
    $data['phone'] = $phone;
    Session::set('code', $data);
    //短信内容 $message
    $message = "您的验证码为" . $num . "，在十分钟内填写，为保障您的账户安全，切勿将验证码泄露于他人。";
    $message = iconv('UTF-8', 'GB2312', $message); //将字符串的编码从GB2312转到UTF-8
    //$content = "您的注册验证码是：".$code."，请在十分钟内填写，切勿将验证码泄露于他人。";
    //var_dump($message);die;
    $gateway = "http://sdk2.028lk.com:9880/sdk2/batchSend2.aspx?CorpID={$uid}&Pwd={$passwd}&Mobile={$telphone}&Content={$message}&Cell=&SendTime=";

    $result = file_get_contents($gateway);
    //$result=1;

    //return $result;//
    return ['status' => $result, 'code' => $num];

}

//电商ID
defined('EBusinessID') or define('EBusinessID', '1304034');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('AppKey') or define('AppKey', 'd7027d67-1d4d-46e7-8abc-80e906def844');
//请求url
defined('ReqURL') or define('ReqURL', 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx');

/**
 * Json方式 查询订单物流轨迹
 *
 *@param $orderId 订单编号
 *@param $ShipperCode 快递公司编码
 *@param $LogisticCode 快递单号
 */
function getOrderTracesByJson($orderId = '', $ShipperCode = '', $LogisticCode = '')
{
    $requestData = "{'OrderCode':'" . $orderId . "','ShipperCode':'" . $ShipperCode . "','LogisticCode':'" . $LogisticCode . "'}";

    $datas = array(
        'EBusinessID' => EBusinessID,
        'RequestType' => '1002',
        'RequestData' => urlencode($requestData),
        'DataType'    => '2',
    );
    $datas['DataSign'] = encrypt($requestData, AppKey);
    $result            = sendPost(ReqURL, $datas);

    //根据公司业务处理返回的信息......

    return $result;
}

/**
 *  post提交数据
 * @param  string $url 请求Url
 * @param  array $datas 提交的数据
 * @return url响应返回的html
 */
function sendPost($url, $datas)
{
    $temps = array();
    foreach ($datas as $key => $value) {
        $temps[] = sprintf('%s=%s', $key, $value);
    }
    $post_data = implode('&', $temps);
    $url_info  = parse_url($url);
    if (empty($url_info['port'])) {
        $url_info['port'] = 80;
    }
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader .= "Host:" . $url_info['host'] . "\r\n";
    $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader .= "Content-Length:" . strlen($post_data) . "\r\n";
    $httpheader .= "Connection:close\r\n\r\n";
    $httpheader .= $post_data;
    $fd = fsockopen($url_info['host'], $url_info['port']);
    fwrite($fd, $httpheader);
    $gets       = "";
    $headerFlag = true;
    while (!feof($fd)) {
        if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
            break;
        }
    }
    while (!feof($fd)) {
        $gets .= fread($fd, 128);
    }
    fclose($fd);

    return $gets;
}

/**
 * 电商Sign签名生成
 * @param data 内容
 * @param appkey Appkey
 * @return DataSign签名
 */
function encrypt($data, $appkey)
{
    return urlencode(base64_encode(md5($data . $appkey)));
}

/**
 *查询要查询用户指定级别内的所有下级id
 *$uid:要查询用户集合
 *$class:要查询的级别
 *$userall:静态变量占位
 *$users:用户集合
 *return----查询指定用户的指定级别内的所有下级id集合(包括自己)
 */
function getchildenallClass($uid, $users, $userall = '', $class = '')
{
    if (empty($userall)) {
        static $userall = [];
    } else {
        static $userall = [];
        $userall        = [];
    }
    if (!in_array($uid, $userall)) {
        if (is_array($uid)) {
            foreach ($uid as $v) {
                $userall[] = $v;
            }
        } else {
            array_push($userall, $uid);
        }
    }
    $userChildren = [];
    foreach ($users as $k => $v) {
        if (is_array($uid)) {
            if (in_array($v['pid'], $uid)) {
                array_push($userChildren, $v['id']);
            }
        } else {
            if ($v['pid'] == $uid) {
                array_push($userChildren, $v['id']);
            }
        }
    }
    $userall = array_unique(array_merge($userall, $userChildren));
    if (!empty($userChildren)) {
        if ($class) {
            $class--;
            if ($class > 0) {
                getchildenallClass($userChildren, $users, '', $class);
            }
        } else {
            getchildenallClass($userChildren, $users);
        }
    }
    sort($userall);

    // dump($userall);
    return $userall;
}

/**
 * 获取指定级别下级
 * @param $uid char 要查询下级的用户集合id；如[1,2,3]
 * @param $num int   要查询的级别
 * @return 查询级别的用户下级
 */
function getChilden($uid, $num = 1)
{
    $user1    = UsersModel::where('pid', 'in', $uid)->field('id,pid')->select();
    $users_id = [];
    foreach ($user1 as $k => $v) {
        $users_id[] = $v['id'];
    }
    for ($i = 1; $i < $num; $i++) {
        if (!$users_id) {
            return $users_id;
        }
        $users_id = getChilden($users_id, $num - 1);
        return $users_id;
    }
    return $users_id;
}

#调用百度地图API获取商店与用户两点之间的驾车距离
#单位 m
function shopDistance($userlng, $userlat, $lng, $lat)
{
    if ($userlng <= 0 && $userlat <= 0) {
        return 0;
    }
    $geturl       = "http://api.map.baidu.com/routematrix/v2/driving?output=json&origins=" . $userlat . "," . $userlng . "&destinations=" . $lat . "," . $lng . "&ak=1ql9hhlWETesceXoCGRkEtGQbw3Haghg";
    $address_data = curlPost($geturl);
    $json_data    = json_decode($address_data, true);
    if ($json_data['message'] == '成功') {
        $distance = $json_data['result'][0]['distance']['value'];
    } else {
        $distance = 0;
    }
    return $distance;
}

function http($url, $data='', $method='GET'){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        exit(curl_error($ch));
    }
    curl_close($ch);
    // 返回结果集
    return $result;
}