<?php
namespace usdt;
use app\common\model\CoinTransfer;
use app\common\model\AccountLog as UserMoneyLog;
use app\common\model\WalletAddress;
use think\Config;
use usdt\tools\CoinClient;

/**
 * USDT 工具类
 * Class usdt
 * @package usdt
 */
class usdt
{
    /**
     * 获取CoinClient 实例
     * @return CoinClient
     * @throws \Exception
     */
    protected static function getCoinClient()
    {
        $config = db('coin')->where('name','usdt')->find();
//        return new CoinClient(Config::get('usdt_config.username'), Config::get('usdt_config.password'), Config::get('usdt_config.ip'), Config::get('usdt_config.port'), 5, array(), 1);
        return new CoinClient($config['zc_wallet_user'],$config['zc_wallet_passwd'],$config['zc_wallet_ip'],$config['zc_wallet_port'], 5, array(), 1);
    }

    /**
     * 通过地址获取钱包余额
     * @param string $address
     * @return int
     * @throws \Exception
     */
    public static function getMoneyByAddress($address)
    {
        /**
         * 获取coin 实例
         */
        $coinClient = self::getCoinClient();
        /**
         * 验证钱包地址
         */
        if(!self::usdt_validateaddress($address)){
            throw new \Exception('钱包地址错误');
        }
        /**
         * 获取钱包信息
         */
        $res = $coinClient-> omni_getbalance($address,31);
        /**
         * 判断是否有余额属性
         */
        if(isset($res['balance'])){
            return $res['balance'];
        }else{
            return 0;
        }
    }

    /**
     * 获取钱包主地址
     * @return null
     * @throws \Exception
     */
    public static function get_master_account_address()
    {
        /**
         * 获取coin 实例
         */
        $coinClient = self::getCoinClient();

        /**
         * 获取主账户地址
         */
        $res = $coinClient -> getaddressesbyaccount('');
        /**
         * 返回主账户地址
         */
        return isset($res[0])?$res[0]:null;
    }

    /**
     * 创建地址
     * @param int $uid
     * @param string $type
     * @return array|mixed|string
     * @throws \Exception
     */
    public static function create_address($uid,$type = null){
        /**
         * 获取coinClient
         */
        $CoinClient = self::getCoinClient();
        $address = db('wallet_address')->where(['user_id'=>$uid,'type'=>'usdt'])->find();
        if($address){
            return $address;
        }
        /**
         * 通过账户id 获取地址
         */
        $qianbao_addr = $CoinClient -> getaddressesbyaccount(strval($uid));
        /**
         * 判断钱包地址是否为数组
         */
        if ((!is_array($qianbao_addr)) || count($qianbao_addr) < 1) {
            /**
             * 创建一个新的地址
             */
            $qianbao_ad = $CoinClient -> getnewaddress(strval($uid));
            /**
             * 判断是否成功
             */
            if (strlen($qianbao_ad) < 1) {
                throw new \Exception('Error generating wallet address 1');
            }else {
                return $qianbao_ad;
            }
        }else {
            return isset($qianbao_addr[0])?$qianbao_addr[0]:null;
        }
    }

    /**
     * 获取交易数量
     */
    public static function check_transaction()
    {
        /**
         * 获取未完成的交易订单
         */
        $transaction_lists = CoinTransfer::where(['status'=>0]) -> select();

        /**
         * 循环处理
         */
        foreach ($transaction_lists as $key=>$item){
            /**
             * 协程处理
             */
            \go(function()use($item){
                /**
                 * 判断交易类型是否为usdt
                 */
                if($item['coin'] == 'usdt'){
                    $trans = self::getCoinClient()->omni_gettransaction($item['txhash']);
                }
                /**
                 * 判断交易类型是否为btc
                 */
                if($item['coin'] == 'btc'){
                    $trans = self::getCoinClient()->gettransaction($item['txhash']);
                }
                /**
                 * 判断确认次数是否大于0
                 */
                if($trans['confirmations'] > 0){
                    /**
                     * 修改交易状态
                     */
                    CoinTransfer::where(['id'=>$item['id']])->update(['status' => 1]);
                }
            });
        }

    }

    /**
     * 获取指定钱包地址的usdt余额
     * @param $address
     * @return array
     * @throws \Exception
     */
    public static function get_total_usdt_money($address)
    {
        $CoinClient = self::getCoinClient();

        return $CoinClient -> omni_getbalance($address,31);
    }
    /**
     * 获取钱包的usdt 数量
     * @return int
     * @throws \Exception
     */
    public static function get_total_usdt_wallet()
    {
        /**
         * 获取coinClient
         */
        $CoinClient = self::getCoinClient();
        /**
         * 查询账户列表
         */
        $own_accounts = $CoinClient->listaccounts();
        /**
         * 定义总金额
         */
        $total_num = 0;
        /**
         * 循环处理账户列表
         */
        foreach($own_accounts as $own_id => $own_account){
            /**
             * 根据账号获取钱包地址
             */
            $own_addresses = $CoinClient->getaddressesbyaccount($own_id . "");

            $res = $CoinClient -> omni_getbalance($own_addresses[0],31);
            /**
             * 获取钱包地址
             */
            $total_num += $res['balance'];
        }
        /**
         * 打印数据
         */
        return $total_num;
    }
    /**
     * 监听USDT钱包
     */
    public static function listen_usdt_wallet()
    {
        \go(function (){
            try{
                /**
                 * 获取coinClient
                 */
                $CoinClient = self::getCoinClient();
                /**
                 * 列出钱包事务
                 */
                $listtransactions = $CoinClient->omni_listtransactions();
                /**
                 * 判断钱包事务 是否存在
                 */
                if((!is_array($listtransactions)) || count($listtransactions) < 1){
                    return ;
                }
                /**
                 * 数组排序
                 */
                krsort($listtransactions);
                /**
                 * 循环处理钱包事务
                 */
                foreach ($listtransactions as $trans){
                    \go(function()use($trans){
                        /**
                         * 过滤钱包类型(USDT id 31) 并且 已经验证过的事件 并且
                         */
                        if(!isset($trans['propertyid'])){
                            return ;
                        }
                        if(!isset($trans['valid'])){
                            return ;
                        }
                        if($trans['propertyid'] != 31 || !$trans['valid']){
                            return;
                        }
                        /**
                         * 根据钱包地址查询账户信息
                         */
                        if(!($wallet_url_info = WalletAddress::where(['type'=>'usdt','url'=>$trans['referenceaddress']]) -> find())){
                            return;
                        }
                        /**
                         * 判断事件是否已经处理过
                         */
                        if($money_log = UserMoneyLog::where(['txid'=>$trans['txid']]) -> find()){
                            /**
                             * 判断确认数是否合法了
                             */
                            if($money_log['status'] == 4 && $trans['confirmations'] > Config::get('usdt_config.confirmations')){
                                /**
                                 * 修改充值状态
                                 */
                                $money_log->status = 1;
                                /**
                                 * 保存数据
                                 */
                                $money_log -> save();
                            }
                            /**
                             * 结束本次处理
                             */
                            return;
                        }
                        /**
                         * 新增记录
                         */
                        UserMoneyLog::add_user_money_log($wallet_url_info['user_id'],0,$trans['amount'],2,3,$trans['confirmations'] > config('usdt_config.confirmations')?1:2,'账户充值',$trans['txid']);
                    });
                }
            }catch (\Throwable $throwable){
                echo "钱包转入监听-异常:".$throwable -> getMessage();
            }
        });
        /**
         * 钱包汇总
         */
        \go(function(){
            try{
                self::usdt_sum2main();
            }catch (\Throwable $throwable){
                echo "钱包汇总-异常:".$throwable -> getMessage();
            }
        });
        /**
         * 交易事务检查
         */
        \go(function(){
            try{
                self::check_transaction();
            }catch (\Throwable $exception){
                echo "交易事务检查-异常:".$exception -> getMessage();
            }
        });
    }

    /**
     * 验证钱包地址
     * @param $address
     * @return bool
     */
    public static function usdt_validateaddress($address)
    {
        try{
            /**
             * 获取ClinClient 实例
             */
            $coinClient = self::getCoinClient();
            /**
             * 调用接口验证 钱包地址
             */
            $valid_res = $coinClient -> validateaddress($address);
            /**
             * 判断是否有效
             */
            if (!$valid_res['isvalid']) {
                return false;
            }else{
                return true;
            }
        }catch (\Throwable $throwable){
            return false;
        }
    }

    /**
     * USDT汇总到主账号
     * @throws \Exception
     */
    public static function usdt_sum2main(){
        /**
         * 获取ClinClient 实例
         */
        $CoinClient = self::getCoinClient();
        /**
         * 获取主账号
         */
        $usdt_coin_base = Config::get('usdt_config.usdt_wallet_coinbase');
        /**
         * 判断钱包是否上锁
         */
        if(Config::get('usdt_config.is_lock')){
            /**
             * 判断密码是否为空
             */
            if(strlen(Config::get('usdt_config.money_password')) < 1){
                throw new \Exception('The password must not be empty.');
            }
            /**
             * 钱包解锁
             */
            $result = $CoinClient -> walletpassphrase(Config::get('usdt_config.money_password'), 32400);
            /**
             * 判断是否解锁失败
             */
            if($result != 'nodata'){
                throw new \Exception("usdt钱包解锁失败");
            }
        }
        /**
         * 查询账户列表
         */
        $accounts = $CoinClient->listaccounts();
        /**
         * 循环账户列表
         */
        foreach($accounts as $id => $btc_balance){
            /**
             * 协程处理
             */
            \go(function ()use($id,$CoinClient,$usdt_coin_base){
                /**
                 * 根据账号获取钱包地址
                 */
                $addresses = $CoinClient->getaddressesbyaccount($id . "");
                /**
                 * 循环账号的下的钱包地址
                 */
                foreach($addresses as $address) {
                    /**
                     * 判断是否为主钱包主地址
                     */
                    if ($address == Config::get('usdt_config.usdt_wallet_coinbase')) {
                        return;
                    }
                    try {
                        /**
                         * 获取账户的usdt余额
                         */
                        $usdt_balance = floatval($CoinClient->omni_getbalance($address, 31)['balance']);
                    } catch (\Throwable $throwable) {
                        // 异常不中断
                        return;
                    }
                    try {
                        /**
                         * 获取 btc 余额
                         */
                        $btc_balance = $CoinClient->getbalance($id . "");
                    } catch (\Throwable $throwable) {
                        // 异常不中断
                        return;
                    }
                    try{
                        /**
                         * 处理钱包汇总逻辑
                         */
                        self::sum2main_do($address,$usdt_balance,$btc_balance,$usdt_coin_base);
                    }catch (\Throwable $throwable){
                        // 异常不中断
                        return ;
                    }
                }
            });
        }
    }

    /**
     * 钱包汇总逻辑
     * @param $address
     * @param $usdt_balance
     * @param $btc_balance
     * @param $usdt_coin_base
     * @throws \Exception
     */
    protected static function sum2main_do($address,$usdt_balance,$btc_balance,$usdt_coin_base){
        /**
         * 判断是否超过了最小值
         */
        if ($usdt_balance >= Config::get('usdt_config.threshold_max')) {
            /**
             * 判断BTC 余额是否小于0.0002
             */
            if ($btc_balance < Config::get('usdt_config.btc_tx_fee_total')) {
                /**
                 * 判断是否转过手续费 还没有到账
                 */
                if (CoinTransfer::where(['from'=>$usdt_coin_base,'to'=>$address,'coin'=>'btc','status'=>0]) -> value("id") < 1) {
                    /**
                     * 转入Btc
                     */
                    $result = self::send_btc($address, Config::get('usdt_config.btc_tx_fee_total'));
                    /**
                     * 判断转入是否成功
                     */
                    if ($result['code']) {
                        try{
                            /**
                             * 新增转入记录
                             */
                            CoinTransfer::add_log($usdt_coin_base,$address,'btc',Config::get('usdt_config.btc_tx_fee_total'),$result['txhash'],0,"usdt转主账号,BTC不足,先转其手续费");
                        }catch (\Throwable $throwable){
                            // 异常不中断
                            echo "异常:".$throwable -> getMessage()."\n";
                            return ;
                        }
                    }
                    else {
                        echo "ERROR BTC 转出失败 \n";
                    }
                }

            }
            /**
             * 判断是否有汇总未成功的
             */
            if (CoinTransfer::where(['from'=>$address,'to'=>$usdt_coin_base,'coin'=>'usdt','status'=>0]) -> value("id") < 1) {
                /**
                 * 汇总usdt
                 */
                $result = self::send_usdt($address, $usdt_coin_base, $usdt_balance);
                /**
                 * 判断汇总是否成功
                 */
                if ($result['code']) {
                    try{
                        /**
                         * 新增记录
                         */
                        CoinTransfer::add_log($address,$usdt_coin_base,'usdt',$usdt_balance,$result['txhash'],0,"usdt转主账号");
                    }catch (\Throwable $exception){
                        echo "异常:".$exception -> getMessage()."\n";
                        // 异常不中断
                        return ;
                    }
                }
                else {
                    echo "ERROR usdt 转出失败 \n";
                }
            }
        }
    }

    /**
     * 发放到 usdt 账户
     * @param $from
     * @param $to
     * @param $amount
     * @return array
     * @throws \Exception
     */
    public static function send_usdt($from, $to, $amount){

        /**
         * 获取CoinClient 实例
         */
        $CoinClient = self::getCoinClient();
        /**
         * 获取客户端信息
         */
        $json = $CoinClient->getinfo();
        /**
         * 获取btc 余额
         */
        $btc_balance = isset($json['balance'])?$json['balance']:0;
        /**
         * 判断btc 余额是否小于0.0002
         */
        if($btc_balance < Config::get('usdt_config.btc_tx_fee_total')){
            throw new \Exception('usdt钱包BTC余额不足');
        }
        /**
         * 返回给定地址和属性的令牌余额
         */
        $usdt_balance = $CoinClient->omni_getbalance($from, 31);
        /**
         * 获取usdt 余额
         */
        $usdt_balance = floatval($usdt_balance['balance']);
        /**
         * 判断转出数量是否大于余额
         */
        if($usdt_balance < $amount){
            throw new \Exception('usdt余额小于转出数量');
        }
        /**
         * 获取交易手续费
         */
        $tx_fee = Config::get('usdt_config.usdt_btc_tx_fee');
        /**
         * 设置交易手续费
         */
        $CoinClient->settxfee(round($tx_fee, 8));
        /**
         * 创建并广播简单的发送事务
         */
        $result = $CoinClient->omni_send($from, $to, 31, $amount . "");
        /**
         * 判断操作是否成功
         */
        if(! stripos($result, "status")){
            return $result;
        }else{
            throw new \Exception("usdt转出失败");
        }
    }

    /**
     * 发放到btc 账户
     * @param $to
     * @param $amount
     * @return array
     * @throws \Exception
     */
    public static function send_btc($to, $amount){
        /**
         * 获取CoinClient 实例
         */
        $CoinClient = self::getCoinClient();
        /**
         * 获取客户端信息
         */
        $json = $CoinClient->getinfo();
        /**
         * 获取钱包余额
         */
        $btc_balance = isset($json['balance'])?$json['balance']:0;

        /**
         * 判断总钱包余额是否小于要交易的金额
         */
        if($btc_balance < $amount){
            throw new \Exception('usdt钱包BTC余额不足');
        }
        /**
         * 获取交易手续费
         */
        $tx_fee = Config::get('usdt_config.usdt_btc_tx_fee');
        /**
         * 设定交易费
         */
        $CoinClient->settxfee(round($tx_fee, 8));
        /**
         * 转账交易
         */
        $result = $CoinClient->sendtoaddress($to, $amount . "");
        /**
         * 判断转出是否成功
         */
        if(!stripos($result, "status")){
            return $result;
        }else{
            throw new \Exception("BTC转出失败");
        }
    }
}