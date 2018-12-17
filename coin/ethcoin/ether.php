<?php
namespace ethcoin;

use app\common\library\Sms;
use app\common\model\AccountLog;
use think\Db;

class ether
{
    /**
     * ETH钱包交互
     */
    public static function get_eth_data($url, $api , $param){
        $data = "{\"jsonrpc\":\"2.0\",\"method\":\"{$api}\",\"params\":{$param},\"id\":1}";

        $ch = curl_init();
        $timeout = 15;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        $error_no = curl_errno($ch);
        curl_close($ch);
        if($error_no){
            return false;
        }else{
            return json_decode($content, true);
        }
    }

    /**
     *  eth转账
     */
    public static function transfer($address,$num)
    {

    }
    /**
     *  eth token 转账
     */
    public static function transfer_token()
    {

    }

    /**
     * 发送Ether
     */
    public static function send_ether($wallet_ip,$wallet_port,$from, $to, $gas_price, $num){
        $num = self::fix_my_num($num, 5);
        $custom_gas_price_hex = dechex($gas_price);
        $amount = bcdechex(bcmul($num, config('coin')['eth']['eth_token_decimals']));
        $eth_address = $wallet_ip;
        $eth_port = $wallet_port;

        $param = array(
            'from' => $from,
            'to' => $to,
            'gas' => "0x5208",  //25200个Gas limit，实际使用21000
            'gasPrice' => "0x{$custom_gas_price_hex}",
            'value' => "0x{$amount}"
        );
        $param = json_encode($param);

        $sendrs = self::get_eth_data("{$eth_address}:{$eth_port}", 'eth_sendTransaction', "[{$param}]");

        return $sendrs;
    }

    /**
     * 发送Ether Token
     */
    public static function send_token($wallet_ip,$wallet_port,$from, $token='sdt', $to, $gas_price, $num){
        $gas = "0x1d4c0";
        if(in_array($token, array('pet', 'ebe','zmot'))){
            $gas = "0xea60";
        }

        $num = self::fix_my_num($num, 5);
        $custom_gas_price_hex = dechex($gas_price);
        $eth_address = $wallet_ip;
        $eth_port = $wallet_port;

        $hex_data = "0xa9059cbb";

        $hex_data .= str_repeat(0, 24);

        $hex_data .= substr($to, 2); //去掉0x

        $amount = bcdechex(bcmul($num, config('coin')[$token]['eth_token_decimals']));

        $hex_data .= str_repeat(0, 64 - strlen($amount)) . $amount;

        $param = array(
            'from' => $from,
            'to' => config('coin')[$token]['eth_token_address'],
            'gas' => $gas,  //120000个Gas limit，实际使用多少不确定，多在30000~60000
            'gasPrice' => "0x{$custom_gas_price_hex}",
            'data' => "{$hex_data}"
        );
        $param = json_encode($param);

        $sendrs = self::get_eth_data("{$eth_address}:{$eth_port}", 'eth_sendTransaction', "[{$param}]");

        return $sendrs;
    }

    /**
     * 查询Ether余额
     */
    public static function get_eth_balance($wallet_ip,$wallet_port,$address){
        $eth_address = $wallet_ip;
        $eth_port = $wallet_port;
        $response = self::get_eth_data("{$eth_address}:{$eth_port}", "eth_getBalance", "[\"{$address}\",\"latest\"]");
        $eth_balance =  hexdec($response['result']) / config('coin')['eth']['eth_token_decimals'];
        return $eth_balance;
    }

    /**
     * 查询Ether Token余额
     */
    public static function get_token_balance($wallet_ip,$wallet_port,$address, $token='sdt'){
        $token_address = config('coin')[$token]['eth_token_address'];
        $eth_address = $wallet_ip;
        $eth_port = $wallet_port;
        $query_data = "0x70a08231000000000000000000000000" . substr($address, 2);
        $response = self::get_eth_data("{$eth_address}:{$eth_port}", "eth_call", "[{\"to\":\"{$token_address}\",\"data\":\"{$query_data}\"},\"latest\"]");
        $token_balance =  hexdec($response['result']) / config('coin')[$token]['eth_token_decimals'];
        return $token_balance;
    }

    public static function fix_my_num($num, $len){
        $arr = explode(".", $num);

        if(count($arr) == 1){
            return $num;
        }

        if(strlen($arr[1]) <= $len){
            return $num;
        }

        $fixed = $arr[0] . '.' . substr($arr[1], 0, $len);

        return floatval($fixed);
    }

    /**
     * 生成以太坊钱包地址
     */
    public static function generate_wallet_address($uid,$type){
        $eth_config = db('coin')->where(['name'=>'eth'])->find();
        $dj_address = $eth_config['zc_wallet_ip'];//ip
        $dj_port =  $eth_config['zc_wallet_port'];//端口
        $wallet_addr = db('wallet_address')->where(['user_id'=>$uid,'type'=>['in',['sdt','eth']]])->value('address');
        if($wallet_addr){
            $address = $wallet_addr;
            $type_address = db('wallet_address')->where(['user_id'=>$uid,'type'=>$type])->find();
            if(!$type_address){
                db('wallet_address')->insert([
                    'user_id' => $uid,
                    'address' => $address,
                    'type' => $type,
                    'created_at' => time(),
                ]);
            }
        }else{
            $wallet_password = $eth_config['zc_wallet_passwd'];
            $wallet_result = self::get_eth_data("{$dj_address}:{$dj_port}", 'personal_newAccount', "[\"{$wallet_password}\"]");
            if(is_array($wallet_result)){
                $address = $wallet_result['result'];
                db('wallet_address')->insert([
                    'user_id' => $uid,
                    'address' => $address,
                    'type' => $type,
                    'created_at' => time(),
                ]);
            }else{
                return ['status'=>0, 'data'=>'Error generating wallet address 1'];
            }
        }
        return ['status'=>1, 'data'=>$address];
    }

    /**
     * 监听转入
     */
    public static function listen_eth_wallet()
    {
        self::eth_watch_zmot();
        $coins = db('Coin')->where("is_eth_token",1)->field('id,name,eth_token_address,eth_token_decimals')->select();
        $coin = $coins[0]['name'];

        $eth_config = db('coin')->where(['name'=>'eth'])->find();
        $dj_address = $eth_config['zc_wallet_ip'];//ip
        $dj_port =  $eth_config['zc_wallet_port'];//端口

        //以太坊系使用同一个钱包
        // $dj_address = config('coin')['eth']['dj_zj']; // ip
        // $dj_port = config('coin')['eth']['dj_dk'];    //端口
        echo 'start ' . $coin . "\n";


        $blockcount_info = self::get_eth_data("{$dj_address}:{$dj_port}", 'eth_blockNumber', '[]');

        if (! is_array($blockcount_info)) {
            echo "###ERR#####***** eth connect fail***** ####ERR####>\n";
            exit;
        }

        echo "Cmplx eth token start,connect ok :\n";

        //最高区块
        if($blockcount_info['result']){
            $block_count = hexdec($blockcount_info['result']);
        }
        if($block_count == 0){
            $blockcount_info = self::get_eth_data("{$dj_address}:{$dj_port}", 'eth_syncing', '[]');
            if($blockcount_info['result']){
                $block_count = hexdec($blockcount_info['result']['currentBlock']);
            }
        }

        echo "{$block_count} blocks total \n";

        $offset = 50;

        if($block_count == 0){
            exit;
        }

        $hash_table = config('REDIS_USER_COIN_ADDR_HASH_NAME');
        $redis = get_redis_instance();

        $max_processed_block_key = "eth_max_processed_block";

        $start_block = intval(session($max_processed_block_key));

        if(!$start_block){
            $start_block = $block_count - 300;
            session($max_processed_block_key, $start_block, 0);
        }

        echo "start_block:{$start_block} \n";

        $block_no_array = array();
        for($i = 0; $i < $offset; $i++){
            $block_no_array[] = $start_block + $i;
        }

        //加上未确认的交易所在的区块高度
        $five_mins_ago = strtotime("-1 minutes");
        $sql = "select id,userid,coinname,txid from suda_myzr where status!=1 and created_at<='{$five_mins_ago}' and coinname in (select name from suda_coin where is_eth_token=1 or name='eth') order by id desc";
        $unconfirmed_txs = Db::query($sql);
        if($unconfirmed_txs){
            foreach ($unconfirmed_txs as $tx_un){
                $trans_un = self::get_eth_data("{$dj_address}:{$dj_port}", 'eth_getTransactionReceipt', "[\"{$tx_un['txid']}\"]");
                if($trans_un){
                    array_unshift($block_no_array, hexdec($trans_un['result']['blockNumber']));
                }
                unset($trans_un);
            }
            unset($tx_un);
        }

        $block_no_array = array_unique($block_no_array);

        foreach($block_no_array as $i){
            $block_no = dechex($i);
            $block_info = self::get_eth_data("{$dj_address}:{$dj_port}", 'eth_getBlockByNumber', "[\"0x{$block_no}\",true]");

            if(!$block_info){
                break;
            }

            if(!$block_info['result']){
                if($block_no <= $block_count){
                    continue;
                }else{
                    break;
                }
            }

            session($max_processed_block_key, $i, 0);

            $transactions = $block_info['result']['transactions'];

            if(count($transactions) == 0){
                continue;
            }

            for($k = 0; $k < count($transactions); $k ++){
                $trans_simple = $transactions[$k];

                $trans_simple_hash = $trans_simple['hash'];

                $find_token = false;

                //如果是从ETH钱包主账号转出的，则是用于Token汇总的手续费，不入账
                $from = $trans_simple['from'];
                if($from == config('coin')['eth']['ETH_WALLET_COINBASE']){
                    continue;
                }

                $receiver = $trans_simple['to'];

                //先判断是以太坊还是以太坊Token
                $hash_key = "eth_{$receiver}";
                $userid = $redis->hGet($hash_table, $hash_key);

                /*
                $user_coin = db('UserCoin')->where(array("ethb" => $receiver))->field('userid')->find();
                if($user_coin){
                    $user = $user_coin;
                }else{
                    $user_coin_addr = db('UserCoinAddr')->where(array("coinname" => 'eth',"addr" => $receiver))->field('userid')->find();
                    $user = $user_coin_addr;
                }
                */

                //以太坊本身
                if($userid){
                    //ETH精确到小数点后18位
                    $value = hexdec($trans_simple['value']) / 1000000000000000000;

                    $myzr = db('Myzr')->where(array('txid' => $trans_simple['hash'], 'coinname' => 'eth'))->find();

                    if (! $myzr) {
                        $myzr_new = array(
                            'userid' => $userid,
                            'username' => $receiver,
                            'coinname' => 'eth',
                            'txid' => $trans_simple['hash'],
                            'num' => $value,
                            'mum' => $value,
                            'fee' => 0,
                            'created_at' => time(),
                            'status' => - config('coin')['eth']['zr_dz']
                        );
                        db('Myzr')->insert($myzr_new);
                        echo "added new myzr record,now height {$i} \n";
                    }else{

                        if($myzr['status'] == 1){
                            continue;
                        }

                        //确认数等于当前区块高度-交易所在区块高度
                        $confirms = $block_count - hexdec($trans_simple['blockNumber']);

                        $num = $myzr['num'];

                        if ($confirms < config('coin')['eth']['zr_dz']) {

                            db('Myzr')->save(array('id' => $myzr['id'], 'status' => intval($confirms - config('coin')['eth']['zr_dz'])));

                            continue;
                        }
                        else {
                            echo "confirmations full,now height {$i}\n";
                            Db::startTrans();
                            try{
                                $res = [];
                                Db::query("select id from suda_myzr where id='{$myzr['id']}' FOR UPDATE");

                                $res[] = AccountLog::add_user_money_log($userid,0,$num,1,3,1,'eth转入');
                                $res[] = db('myzr')->update(array('id' => $myzr['id'], 'status' => 1));
                                if (check_arr($res)) {
                                    Db::commit();
                                    echo $value . ' receive ok ' . 'eth' . ' ' . $num;
                                    echo "commit ok\n";
                                    //发送短信通知
                                    $user = db('User')->where(array('id' => $userid))->value('phone');
                                    //短信通知买方
                                    $content = "您的eth 转入：{$value}";
                                    Sms::sends($user, $content);
                                }
                                else {
                                    throw new \Exception("receive fail eth ".$num .json_encode($res));
                                }
                            } catch (\Exception $e) {
                                Db::rollback();
                                dump($e->getMessage());
                            }


                        }
                    }
                }else{ //以太坊Token
                    echo "not eth,maybe token,now height {$i}\n";
                    for($t = 0; $t < count($coins); $t++){
                        $token = $coins[$t];
                        if($trans_simple['to'] == strtolower($token['eth_token_address'])){
                            $coin = $token['name'];
                            echo "find {$coin},now height {$i} \n";
                            $find_token = true;
                            break;
                        }
                    }
                }

                if(!$find_token){
                    echo "not eth token \n";
                    continue;
                }

                $trans = self::get_eth_data("{$dj_address}:{$dj_port}", 'eth_getTransactionReceipt', "[\"{$trans_simple['hash']}\"]");
                $trans = $trans['result'];

                $trans_status = $trans['status'];

                //失败的交易，忽略
                if($trans_status == "0x0"){
                    echo "token trans failed \n";
                    continue;
                }

                $xchain_infos = $trans['logs'];

                foreach($xchain_infos as $xchain_info){
                    $receiver = str_replace("000000000000000000000000","",$xchain_info['topics'][2]);


                    $value = hexdec($xchain_info['data']) / $token['eth_token_decimals'];

                    echo "zr value {$value} \n";

                    if(!$value){
                        continue;
                    }

                    $hash_key = "{$coin}_{$receiver}";
                    $userid = $redis->hGet($hash_table, $hash_key);
                    if(!$userid){
                        echo "no account find continue,now height {$i}\n";
                        continue;
                    }

                    /*
                    if (!($user = db('UserCoin')->where(array("{$coin}b" => $receiver))->value('userid'))) {
                        if (!($user = db('UserCoinAddr')->where(array("coinname" => $coin,"addr" => $receiver))->value('userid'))) {
                            echo "no account find continue\n";
                            continue;
                        }
                    }
                    */

                    $myzr = db('Myzr')->where(array('txid' => $trans['transactionHash'], 'coinname' => $coin))->find();

                    if (!$myzr) {
                        $myzr_new = array(
                            'userid' => $userid,
                            'username' => $receiver,
                            'coinname' => $coin,
                            'txid' => $trans['transactionHash'],
                            'num' => $value,
                            'mum' => $value,
                            'fee' => 0,
                            'created_at' => time(),
                            'status' => - config('coin')[$coin]['zr_dz']
                        );
                        db('Myzr')->insert($myzr_new);
                        echo "added new myzr record,now height {$i} \n";
                    }else{

                        if($myzr['status'] == 1){
                            continue;
                        }

                        //确认数等于当前区块高度-交易所在区块高度
                        $confirms = $block_count - hexdec($trans_simple['blockNumber']);

                        $num = $myzr['num'];

                        if ($confirms < config('coin')[$coin]['zr_dz']) {

                            db('Myzr')->save(array('id' => $myzr['id'], 'status' => intval($confirms - config('coin')[$coin]['zr_dz'])));

                            continue;
                        }
                        else {
                            echo "confirmations full,now height {$i}\n";
                            Db::startTrans();
                            try{
                                $res = [];
//                                Db::query("select id from suda_user_coin where userid = $userid for update");//add long 行锁
                                Db::query("select id from suda_myzr where id = {$myzr['id']} for update");//add long 行锁
                                $type_coin = ['eth'=>1,'usdt'=>2,'sdt'=>3];
                                $res[] = AccountLog::add_user_money_log($userid,0,$num,$type_coin[$coin],3,1,$coin.'转入');
//                                $res[] = db('user_coin')->where(array('userid' => $userid))->setInc($coin, $num);
                                $res[] = db('myzr')->update(array('id' => $myzr['id'], 'status' => 1));
                                if (check_arr($res)) {
                                    Db::commit();
                                    echo $value . ' receive ok ' . $coin . ' ' . $num;
                                    echo "commit ok\n";
                                    //发送短信通知
                                    $user = db('User')->where(array('id' => $userid))->value('phone');
                                    //短信通知买方
                                    $content = "您的{$coin} 转入：{$value}";
                                    Sms::sends($user, $content);
                                }
                                else {
                                    throw new \Exception("$value . 'receive fail ' . $coin . ' ' . $num");
                                }
                            } catch (\Exception $e) {
                                Db::rollback();
                                dump($e->getMessage());
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     *
     */
    public static function eth_watch_zmot()
    {
        $eth_config = db('coin')->where(['name'=>'eth'])->find();
        $dj_address = $eth_config['zc_wallet_ip'];//ip
        $dj_port =  $eth_config['zc_wallet_port'];//端口
        // $dj_address = config('coin')['eth']['dj_zj'];
        // $dj_port = config('coin')['eth']['dj_dk'];
        $url = "{$dj_address}:{$dj_port}";

        $new_filter = self::get_eth_data($url, "eth_newFilter", "[{\"fromBlock\":\"0x5f5cc4\",\"toBlock\":\"latest\",\"address\":\"0x705f4dc0ede01a98e5b3c55d5b97e1d667cd2a7a\"}]");

//        if (!in_array($new_filter['result'])) {
            $new_filter_id = $new_filter['result'];

            $transactions = self::get_eth_data($url, "eth_getFilterLogs", "[\"{$new_filter_id}\"]");

            $transactions = $transactions['result'];
            var_dump(count($transactions));
            $find_count = 0;
            foreach ($transactions as $key => $transaction) {
                $topics = $transaction['topics'];
                $from = str_replace("000000000000000000000000", "", $topics[1]);
                $to = str_replace("000000000000000000000000", "", $topics[2]);

                $has_address = db('wallet_address')->where("address='{$to}'")->count();

                if ($has_address) {
                    $has_origin = self::check_zmot_origin($from, $key, $transactions);
                    if ($has_origin) {
                        $find_count += 1;
                        $txhash = $transaction['transactionHash'];

                        // if (!M('MyzrIgnore')->where("txid='{$txhash}'")->getField("id")) {
                        //     M('MyzrIgnore')->add(array('txid' => $txhash, 'coinname' => 'zmot', 'addtime' => time()));
                        // }
                    }
                }
            }

            var_dump($find_count);

            self::get_eth_data($url, "eth_uninstallFilter", "[\"{$new_filter_id}\"]");
//        }

    }

    /**
     *
     */
    public static function check_zmot_origin($from, $end, $transactions){
        $origins = array(
            '0xe925c8dec73cc682c69ff4ad281543c9c11c1a81',
            '0xfc85382f0fee4e8696fbe5338b91f2a254c26505',
            '0x2f5d9a971f2a3f4cb3104aaeb92211daaea37e4f',
            '0x8ae15e92d1a50f5aeed8f38c8715f61dd8d8d0cc',
            '0x884cda44f3f991ed376cc1f29d5475e25c55a6a9',
        );

        if(in_array($from, $origins)){
            return $from;
        }

        for($i=0; $i < $end; $i++){
            $transaction = $transactions[$i];
            $topics = $transaction['topics'];
            $from_tmp = str_replace("000000000000000000000000","", $topics[1]);
            $to_tmp = str_replace("000000000000000000000000","", $topics[2]);

            if($to_tmp == $from){
                if(in_array($from_tmp, $origins)){
                    return $from_tmp;
                }
                if($end >= 1){
                    self::check_zmot_origin($from_tmp, $end -1, $transactions);
                }
            }
        }

    }



    /**
     * 汇总
     */

    public static function eth_sum2main(){
        $eth_config = db('coin')->where(['name'=>'sdt'])->find();

        $sql = "select id,name,eth_token_address,eth_token_decimals,zc_min from suda_coin where is_eth_token='1' and name in ('sdt') ";

        $eth_tokens = Db::query($sql);

        $coinbase = config('coin')['eth']('ETH_WALLET_COINBASE');
        $passwords = explode(",", base64_decode(safe_filter($eth_config['zc_wallet_passwd'])));
        // $passwords = explode(",", base64_decode(safe_filter($_REQUEST['password'])));
        $eth_address = $eth_config['zc_wallet_ip'];//ip
        $eth_port = $eth_config['zc_wallet_port'];//端口
        // $eth_port = config('coin')['eth']['zc_wallet_port'];

        $all_addrs = self::get_eth_data("{$eth_address}:{$eth_port}", 'eth_accounts', '[]');
        $all_addrs = $all_addrs['result'];

        $gas_price = self::get_eth_data("{$eth_address}:{$eth_port}", 'eth_gasPrice', '[]');
        $gas_price = hexdec(substr($gas_price['result'],2));

        $custom_gas_price = $gas_price * 1.3;

        $cost_ether = $custom_gas_price * 21000 / config('coin')['eth']['eth_token_decimals'];

        echo "gasPrice:{$custom_gas_price} \n";
        echo "cost_ether:{$cost_ether} \n";


        foreach($all_addrs as $address){
            if($address == $coinbase){
                continue;
            }
            //先查以太坊
            $eth_balance =  self::get_eth_balance($eth_address, $eth_port, $address);

            // Eth余额0.5以上转到主钱包
            if($eth_balance  >= 0.45) {
                echo "{$address} Eth余额{$eth_balance}，开始汇总\n";
                $unlock = false;
                foreach($passwords as $password){
                    $unlock = self::get_eth_data("{$eth_address}:{$eth_port}", 'personal_unlockAccount', "[\"{$address}\",\"{$password}\",null]");
                    if (!is_array($unlock) || $unlock['error']) {
                        $unlock = false;
                    }else{
                        $unlock = true;
                        break;
                    }
                }

                if(!$unlock){
                    echo "{$address} unlock失败\n";
                    continue;
                }else{
                    echo "{$address} unlock成功\n";
                }

                $amount = $eth_balance - ($cost_ether * 4);

                $amount = self::fix_my_num($amount, 4);
                $sendrs = self::send_ether($eth_address, $eth_port, $address, $coinbase, $custom_gas_price, $amount);

                self::get_eth_data("{$eth_address}:{$eth_port}", 'personal_lockAccount', "[\"{$address}\"]");

                //ETH转出日志
                if ($sendrs && !$sendrs['error']) {
                    $new_transfer = array(
                        'from' => $address,
                        'to' => $coinbase,
                        'coin' => 'eth',
                        'value' => $amount,
                        'txhash' => $sendrs['result'],
                        'remark' => "eth转主账号",
                        'created_at' => time(),
                        'gas_price' => $custom_gas_price,
                    );
                    db('CoinTransfer')->insert($new_transfer);
                }
                echo "{$address} 汇总Eth {$amount}\n";
            }

            echo "{$address} Eth余额{$eth_balance}，不足汇总，开始循环Token\n";

            //continue;
            //循环Token
            foreach ($eth_tokens as $eth_token){
                $coin = $eth_token['name'];

                $token_balance =  self::get_token_balance($eth_address, $eth_port, $address, $coin);
                echo "{$address} {$coin}余额{$token_balance}\n";
                $threshold = config('coin')[$coin]['zc_min'] * 10;

                if($token_balance  >= $threshold){
                    echo "{$address} {$coin}余额{$token_balance}，开始汇总\n";
                    //检查ETH余额是否够手续费
                    $eth_balance =  self::get_eth_balance($eth_address, $eth_port, $address);
                    echo "{$address} Eth {$eth_balance} \n";
                    $eth_fee_back = $cost_ether * 3;

                    if($eth_balance < $eth_fee_back){
                        echo "{$address} Eth不足，主账号先转手续费";
                        //从主账号发送ETH到该账号

                        $unlock = false;
                        foreach($passwords as $password){
                            $unlock = self::get_eth_data("{$eth_address}:{$eth_port}", 'personal_unlockAccount', "[\"{$coinbase}\",\"{$password}\",null]");
                            if (!is_array($unlock) || $unlock['error']) {
                                $unlock = false;
                            }else{
                                $unlock = true;
                                break;
                            }
                        }

                        if(!$unlock){
                            echo "{$coinbase} unlock失败 \n";
                            exit;
                        }else{
                            echo "{$coinbase} unlock成功 \n";
                        }

                        $sendrs = self::send_ether($eth_address, $eth_port, $coinbase, $address, $custom_gas_price, $cost_ether * 6);

                        self::get_eth_data("{$eth_address}:{$eth_port}", 'personal_lockAccount', "[\"{$address}\"]");
                        //ETH转出日志
                        if ($sendrs && !$sendrs['error']) {
                            $new_transfer = array(
                                'from' => $coinbase,
                                'to' => $address,
                                'coin' => 'eth',
                                'value' => $cost_ether * 6,
                                'txhash' => $sendrs['result'],
                                'remark' => "{$coin}转主账号，ETH不足，先给其转手续费",
                                'created_at' => time(),
                                'gas_price' => $custom_gas_price,
                            );
                            db('CoinTransfer')->insert($new_transfer);
                        }

                        echo "{$coinbase} 转出Eth {$eth_fee_back}\n";


                        continue;
                    }else{
                        $unlock = false;
                        foreach($passwords as $password){
                            $unlock = self::get_eth_data("{$eth_address}:{$eth_port}", 'personal_unlockAccount', "[\"{$address}\",\"{$password}\",null]");
                            if (!is_array($unlock) || $unlock['error']) {
                                $unlock = false;
                            }else{
                                $unlock = true;
                                break;
                            }
                        }

                        if(!$unlock){
                            echo "{$address} unlock失败\n";
                            continue;
                        }else{
                            echo "{$address} unlock成功\n";
                        }

                        $token_balance = self::fix_my_num($token_balance, 4);
                        //PET貌似不能转空，得留点儿
                        if(in_array($eth_token['name'], array('pet', 'ebe'))){
                            $token_balance = $token_balance - 1;
                        }
                        $sendrs = self::send_token($eth_address, $eth_port, $address, $eth_token['name'], $coinbase, $custom_gas_price, $token_balance);

                        self::get_eth_data("{$eth_address}:{$eth_port}", 'personal_lockAccount', "[\"{$address}\"]");
                        //ETH转出日志
                        if ($sendrs && !$sendrs['error']) {
                            $new_transfer = array(
                                'from' => $address,
                                'to' => $eth_token['eth_token_address'],
                                'coin' => $coin,
                                'value' => $token_balance,
                                'txhash' => $sendrs['result'],
                                'remark' => "{$coin}转主账号",
                                'created_at' => time(),
                                'gas_price' => $custom_gas_price,
                            );
                            db('CoinTransfer')->insert($new_transfer);
                        }

                        echo "{$address} 汇总{$coin} {$token_balance} \n";
                    }
                }
            }
        }
        echo "eth sum completed! \n";
    }

}


