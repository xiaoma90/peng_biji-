<?php
namespace alipayment;
use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\NotifyContext;
use Payment\Client\Transfer;
use Payment\Config;
use AopClient;
use AlipayTradeAppPayRequest;
/**
* 支付宝
*/
class Alipay{
	# 创建订单
	public static function create($data,$channel='ali_wap'){
		# 读取支付宝配置
		$config = config('Alipay');
		# 默认手机站支付
		$channel;
		# 支付的数据
		$payData = [
			# 商品信息
		    'body' => $data['body'],
		    # 订单名称
		    'subject' => $data['subject'],
		    # 商家支付订单号
		    'order_no' => $data['order_no'],
		    # 订单过期时间
		    'timeout_express' => $data['timeout_express'],
		    # 订单总额
		    'amount' => $data['amount'],
		    # 支付成功返回页面
		    'return_param' => $data['return_param'],
		    # 商品类型1=商品0=虚拟货币
		    'goods_type' => 1,
		    'store_id' => '',// 没有就不设置
		];
		try {
			# 下单
		    $payUrl = Charge::run($channel, $config, $payData);
		} catch (PayException $e) {
		    // 打印错误
		    echo($e -> errorMessage());
		    exit();
		}
		# 返回下单结果的支付url
		return $payUrl;
	}
	# 回调
	/*public static function callback($fun){
		#实例化
		$result = new NotifyContext;
		填写需要参数
		$data = ['app_id'=>C('app_id','alipay'),'notify_url'=>C('notify_url','alipay'),'return_url'=>C('return_url','alipay'),'sign_type'=>C('sign_type','alipay'),'ali_public_key'=>C('ali_public_key','alipay'),'rsa_private_key'=>C('rsa_private_key','alipay')];
		$data = config('alipay');
		unset($data['merchant_private_key']);
		unset($data['charset']);
		unset($data['gatewayUrl']);
		# 校验信息
		$result -> initNotify('ali_charge',$data);

		# 接受返回信息
		$information = $result -> getNotifyData();
		# 判断支付状态
		if($information['trade_status']=='TRADE_SUCCESS'){
			$fun($information);

		}
		exit('success');
	}*/
	#app参数
	/*public static function getAlipayOrderstring(){
		include "../alipay-sdk/AopSdk.php";
		$aliConfig = config('Alipay');
		$aop = new AopClient;
		$aop->gatewayUrl = $aliConfig['gatewayUrl'];
		$aop->appId = $aliConfig['app_id'];
		$aop->rsaPrivateKey = $aliConfig['merchant_private_key'];
		$aop->format = "json";
		$aop->charset = "UTF-8";
		$aop->signType = "RSA1";
		$aop->alipayrsaPublicKey = $aliConfig['ali_public_key'];
		//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
		$request = new AlipayTradeAppPayRequest();
		//SDK已经封装掉了公共参数，这里只需要传入业务参数
		$bizcontent = "{\"body\":\"我是测试数据\"," 
		                . "\"subject\": \"App支付测试\","
		                . "\"out_trade_no\": \"20170125test01\","
		                . "\"timeout_express\": \"30m\"," 
		                . "\"total_amount\": \"0.01\","
		                . "\"product_code\":\"QUICK_MSECURITY_PAY\""
		                . "}";
		$request->setNotifyUrl("商户外网可以访问的异步地址");
		$request->setBizContent($bizcontent);
		//这里和普通的接口调用不同，使用的是sdkExecute
		$response = $aop->sdkExecute($request);
		//htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
		return htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
	}*/
	# 支付宝付款接口
	public static function querys($data = []){
		$aliConfig = config('Alipay');
		// dd($aliConfig);
		$default = [
		    'trans_no' => time(),
		    'payee_type' => 'ALIPAY_LOGONID',
		    'payee_account' => '15538147923',
		    'amount' => '0.01',
		    'remark' => '提现调用测试',
		    'payee_real_name' => '刘广财',
		];
		# 合并配置
		$data = array_merge($default,$data);
		// dump(Transfer::run(Config::ALI_TRANSFER, $aliConfig, $data));die;
		try {
		    $ret = Transfer::run(Config::ALI_TRANSFER, $aliConfig, $data);
		} catch (PayException $e) {
		    return ['msg'=>$e->errorMessage()];
		}

		$res = json_encode($ret, JSON_UNESCAPED_UNICODE);
		return json_decode($res,true);
	}
}