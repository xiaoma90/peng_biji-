<?php
namespace messages;




/**
* 金帮手短信对接
*/
class NewSms()
{
	protected $phone;
	protected $username;
	protected $password;
	
	function __construct($phone)
	{
		$this->phone = $phone;
		$this->username = "QMSW";
		$this->password = md5("hH5hP2gJ");
	}

	function sendSms(){
		$str 	= "1234567890123456789012345678901234567890";
	    $str 	= str_shuffle($str);
	    $code	= substr($str,3,6);
	    $data 	= "username=%s&password=%s&mobile=%s&content=%s";
	    $url  	= "http://120.55.248.18/smsSend.do?";
	    $pass 	= md5($this->username.$this->password);
	    $content = "您的验证码是：".$code."，请在10分钟内填写，切勿将验证码泄露于他人。【芊铭生物】";
	    $content = urlencode($content);
	    $rdata = sprintf($data, $this->username, $pass, $this->phone, $content);
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_POST,1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return ['code' => $code, 'data' => $result, 'msg' => ''];
	}


}
