<?php
namespace chinapay;

//配置文件
//
class ChinaConfig
{
	const VERSION = '5.1.0';//报文版本号，固定5.1.0，请勿改动
	const SIGNMETHOD = '01';//签名方式，证书方式固定01，请勿改动
	//是否验证验签证书的CN，测试环境请设置false，生产环境请设置true。非false的值默认都当true处理。
	const IFVALIDATECNNAME = true;
	//是否验证https证书，测试环境请设置false，生产环境建议优先尝试true，不行再false。非true的值默认都当false处理。
	const IFVALIDATEREMOTECERT = true;
	
	// 前台请求地址
	const SDK_FRONT_TRANS_URL = 'https://gateway.test.95516.com/gateway/api/frontTransReq.do';
	// 后台请求地址
	const SDK_BACK_TRANS_URL  = 'https://gateway.test.95516.com/gateway/api/backTransReq.do';
	// 批量交易
	const SDK_BATCH_TRANS_URL = 'https://gateway.test.95516.com/gateway/api/batchTrans.do';
	//单笔查询请求地址
	const SDK_SINGLE_QUERY_URL = 'https://gateway.test.95516.com/gateway/api/queryTrans.do';
	//文件传输请求地址
	const SDK_FILE_QUERY_URL   = 'https://filedownload.test.95516.com/';
	//有卡交易地址
	const SDK_Card_Request_Url = 'https://gateway.test.95516.com/gateway/api/cardTransReq.do';
	//App交易地址
	const SDK_App_Request_Url  = 'https://gateway.test.95516.com/gateway/api/appTransReq.do';


	// 前台通知地址 (商户自行配置通知地址)
	const SDK_FRONT_NOTIFY_URL = 'http://rj.runjiaby.com/index/china_pay/backurl';
	// 后台通知地址 (商户自行配置通知地址，需配置外网能访问的地址)
	const SDK_BACK_NOTIFY_URL  = 'http://rj.runjiaby.com/index/china_pay/frontUrl';

	/** 以下缴费产品使用，其余产品用不到，无视即可 */
	// 前台请求地址
	const JF_SDK_FRONT_TRANS_URL  = 'https://gateway.test.95516.com/jiaofei/api/frontTransReq.do';
	// 后台请求地址
	const JF_SDK_BACK_TRANS_URL   = 'https://gateway.test.95516.com/jiaofei/api/backTransReq.do';
	// 单笔查询请求地址
	const JF_SDK_SINGLE_QUERY_URL = 'https://gateway.test.95516.com/jiaofei/api/queryTrans.do';
	// 有卡交易地址
	const JF_SDK_CARD_TRANS_URL   = 'https://gateway.test.95516.com/jiaofei/api/cardTransReq.do';
	// App交易地址
	const JF_SDK_APP_TRANS_URL    = 'https://gateway.test.95516.com/jiaofei/api/appTransReq.do';

	//入网测试环境签名证书配置 
	// 多证书的情况证书路径为代码指定，可不对此块做配置。
	// 签名证书路径，必须使用绝对路径，如果不想使用绝对路径，可以自行实现相对路径获取证书的方法；测试证书所有商户共用开发包中的测试签名证书，生产环境请从cfca下载得到。
	// 测试环境证书位于assets/测试环境证书/文件夹下，请复制到d:/certs文件夹。生产环境证书由业务部门邮件发送。
	//windows样例：/uploads/goods/20170812/32a7951489501e07acc917b4ec7f46fe.jpg
	const SDK_SIGN_CERT_PATH_W   = 'http://rj.runjiaby.com/zheng/yinlian.pfx';
	// linux样例（注意：在linux下读取证书需要保证证书有被应用读的权限）（后续其他路径配置也同此条说明）
	// const SDK_SIGN_CERT_PATH_L = '/SERVICE01/usr/ac_frnas/conf/ACPtest/acp_test_sign.pfx';

	//签名证书密码，测试环境固定000000，生产环境请修改为从cfca下载的正式证书的密码，正式环境证书密码位数需小于等于6位，否则上传到商户服务网站会失败
	const SDK_SIGN_CERT_PWD = '111111';

	//加密证书配置
	//敏感信息加密证书路径(商户号开通了商户对敏感信息加密的权限，需要对 卡号accNo，pin和phoneNo，cvn2，expired加密（如果这些上送的话），对敏感信息加密使用)
	//// 密码加密证书（这条一般用不到的请随便配）
	const SDK_ENCRYPT_CERT_PATH ='http://rj.runjiaby.com/zheng/cp.cer';

	// 验签证书配置
	//验签中级证书（证书位于assets/测试环境证书/文件夹下，请复制到d:/certs文件夹）
	const MIDDLECERT_PATH = 'http://rj.runjiaby.com/zheng/cp.cer';
	// 验签根证书（证书位于assets/测试环境证书/文件夹下，请复制到d:/certs文件夹）
	const ROOTCERT_PATH   = 'http://rj.runjiaby.com/zheng/cp.cer';


	// 验签证书路径（请配到文件夹，不要配到具体文件）
	const SDK_VERIFY_CERT_DIR = 'http://rj.runjiaby.com/zheng/';

	//文件下载目录 
	const SDK_FILE_DOWN_PATH = 'http://rj.runjiaby.com/zheng/';
	//日志配置	配置目录
	const SDK_LOG_FILE_PATH = 'http://rj.runjiaby.com/zheng/';
	// 日志级别，debug级别会打印密钥，生产请用info或以上级别
	const LOGLEVEL= 'DEBUG';
	// //日志级别，关掉的话改PhpLog::OFF
	// const SDK_LOG_LEVEL = PhpLog::DEBUG;
	const SECUREKEY = '';
	const VALIDATECERT_DIR = '';
}
