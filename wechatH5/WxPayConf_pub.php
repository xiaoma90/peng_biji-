<?php
/**
 * 	配置账号信息
 */
namespace wechatH5;

class WxPayConf_pub {
    //=======【基本信息设置】=====================================
    //微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
    const APPID = 'wx6d16571bcb68829c';
    //受理商ID，身份标识
    // const MCHID = '1501992461';
    const MCHID = '1501992431';
    //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
    //meihuishangmao
    // const KEY = '5c7b0e6a0c4a6d1bae83888bbac8442c';
    const KEY = 'a4a10766aea350ca997ddbf0c69910b4';
    //JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
    const APPSECRET = '6ddf21baba15a4b923a80b6f661d7f8d';
    //=======【交易类型设置】===================================
    //H5支付的交易类型为MWEB
//    const TRADE_TYPE= 'MWEB';

    //公众号支付的交易类型为JSAPI
//    const TRADE_TYPE= 'JSAPI';
        //开放平台APP支付
//    const TRADE_TYPE= 'APP';
    const App_id = 'wx361e391598e939c8';
    const Mch_id = '1498268542';
    const Key = 'df74d08621f8a9bb64beadf2cc9922c1';
/*    const App_id = 'wx64bec70385819d6a';
    const Mch_id = '1487093452';
    const Key = '03064f4821c2eb0aee9150b9bb0707f6';*/
//    //=======【JSAPI路径设置】===================================
//    //获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
    const JS_API_CALL_URL = 'http://admin.mhsc123.com/index/wxpay/wechatPay';
//    //手动授权,跳转页面,绑定微信
    const JS_API_BIND_URL = 'http://admin.mhsc123.com/index/wechatlogin/index';
    //=======【证书路径设置】=====================================
    //证书路径,注意应该填写绝对路径
    const SSLCERT_PATH = __DIR__.'/cacert/apiclient_cert.pem';
    const SSLKEY_PATH = __DIR__.'/cacert/apiclient_key.pem';
    //=======【异步通知url设置】===================================
    //异步通知url，商户根据实际开发过程设定
    const NOTIFY_URL = 'http://admin.mhsc123.com/index/Notify/wechatNotify';

    //=======【curl超时设置】===================================
    //本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
    const CURL_TIMEOUT = 30;



}
?>