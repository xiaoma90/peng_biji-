<?php
namespace usdt\tools;

use GuzzleHttp\Client;

/**
 * 货币客户端
 * Class CoinClient
 * @package usdt\tools
 * @method array getinfo() 获取当前客户端信息
 * @method array|string getaddressesbyaccount() 根据账号获取钱包地址
 * @method array|string getnewaddress() 获取新钱包地址
 * @method array omni_listtransactions() 列出钱包事务
 * @method array listaccounts() 查询账户列表
 * @method array omni_gettransaction() 获取有关Omni事务的详细信息
 * @method array gettransaction() 获取有关事务的详细信息
 * @method array walletpassphrase() 钱包解锁
 * @method array omni_getbalance() 返回给定地址和属性的令牌余额
 * @method array getbalance() 取得余额
 * @method array settxfee() 设定交易费
 * @method array sendtoaddress() 付款 交易
 */
class CoinClient
{
    /**
     * 远程接口地址
     * @var string
     */
    private $url;
    /**
     * 请求超时时间
     * @var int
     */
    private $timeout;
    /**
     * 用户名
     * @var
     */
    private $username;
    /**
     * 密码
     * @var
     */
    private $password;
    public $is_batch = false;
    public $batch = array();
    public $debug = false;
    public $jsonformat = false;
    public $res = '';
    /**
     * 默认的请求头信息
     * @var array
     */
    private $headers = array(
        'User-Agent: CDCHAINS Rpc',
        'Content-Type: application/json',
        'Accept: application/json',
        'Connection: close'
    );
    public $ssl_verify_peer = true;

    /**
     * 构造器
     * CoinClient constructor.
     * @param $username
     * @param $password
     * @param $ip
     * @param $port
     * @param int $timeout
     * @param array $headers
     * @param bool $jsonformat
     * @throws \Exception
     */
    public function __construct($username, $password, $ip, $port, $timeout = 3, $headers = array(), $jsonformat = false)
    {
        $this->url = 'http://' . $ip . ':' . $port;
        $this->username = $username;
        $this->password = $password;
        $this->timeout = $timeout;
        $this->headers = array_merge($this->headers, $headers);
        $this->jsonformat = $jsonformat;
        /**
         * 获取信息
         */
        $json = $this->getinfo();
        /**
         * 判断链接是否失败
         */
        if (!isset($json['version']) || !$json['version']) {
            throw new \Exception('Wallet link failed');
        }
    }

    /**
     * 装饰者
     * @param $method
     * @param array $params
     * @return string
     */
    public function __call($method, array $params)
    {
        if ((count($params) === 1) && is_array($params[0])) {
            $params = $params[0];
        }

        $res = $this->execute($method, $params);
        return $res ? $res : $this->res;
    }

    /**
     * 调用指定的接口
     * @param $procedure
     * @param array $params
     * @return string
     */
    public function execute($procedure, array $params = array())
    {
        return $this->doRequest($this->prepareRequest($procedure, $params));
    }

    /**
     * 构建请求的基本参数
     * @param $procedure
     * @param array $params
     * @return array
     */
    public function prepareRequest($procedure, array $params = array())
    {
        $payload = array('jsonrpc' => '2.0', 'method' => $procedure, 'id' => mt_rand());

        if (!empty($params)) {
            $payload['params'] = $params;
        }
        return $payload;
    }

    /**
     * 执行请求
     * @param array $payload
     * @return string
     */
    private function doRequest(array $payload)
    {
        /**
         * 备选方案 代替网络请求
         */
//        $res = (new Client()) -> request('POST',$this -> url,[
//            'headers'=>[
//                'User-Agent'=>'CDCHAINS Rpc',
//                'Content-Type'=>'application/json',
//                'Accept'=>'application/json',
//                'Connection'=>'close',
//                'Authorization'=>'Basic ' . base64_encode($this->username . ':' . $this->password)
//            ],
//            'body'=>json_encode($payload)
//        ]) -> getBody() -> getContents();
//        dump($res);die();
        $stream = @fopen(trim($this->url), 'r', false, $this->getContext($payload));

        if (!is_resource($stream)) {
            throw new \Exception('Unable to establish a connection');
        }

        $metadata = stream_get_meta_data($stream);
        $response_text = stream_get_contents($stream);
        $response_text = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response_text);

        $response = json_decode($response_text, true);

        $header_1 = $metadata['wrapper_data'][0];
        preg_match('/[\\d]{3}/i', $header_1, $code);
        $code = trim($code[0]);

        if ($code == '200') {
            return isset($response['result']) ? $response['result'] : 'nodata';
        }
        else {
            if ($response['error'] && is_array($response['error'])) {
                $detail = 'code=' . $response['error']['code'] . ',message=' . $response['error']['message'];
                throw new \Exception('SERVER 返回' . $code . '[' . $detail . ']');
            }
            else {
                throw new \Exception('SERVER 返回' . $code);
            }
        }
    }

    /**
     * 获取请求内容
     * @param array $payload
     * @return resource
     */
    private function getContext(array $payload)
    {
        $headers = $this->headers;
        if (!empty($this->username) && !empty($this->password)) {
            $headers[] = 'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password);
        }

        return stream_context_create(array(
            'http' => array('method' => 'POST', 'protocol_version' => 1.1000000000000001, 'timeout' => $this->timeout, 'max_redirects' => 2, 'header' => implode("\r\n", $headers), 'content' => json_encode($payload), 'ignore_errors' => true),
            'ssl'  => array('verify_peer' => $this->ssl_verify_peer, 'verify_peer_name' => $this->ssl_verify_peer)
        ));
    }

    /*hide long 2017-09-05
        protected function debug($str)
        {
            if (is_array($str)) {
                $str = implode('#', $str);
            }

            debug($str, 'CoinClient');
        }
    */

//    protected function error($str)
//    {
//        if ($this->jsonformat) {
//            $this->res = json_encode(array('data' => $str, 'status' => 0));
//        }
//        else {
//            echo json_encode(array('info' => $str, 'status' => 0));
//            exit();
//        }
//    }
}


?>
