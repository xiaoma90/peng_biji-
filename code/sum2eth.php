<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2018/12/21
 * Time: 10:26
 */
 function usdt_sum2main(){
    $password = base64_decode(safe_filter($_REQUEST['password']));

    if(! $password){
        echo "ERROR 密码不能为空 \n";
        exit;
    }

    $threshold_min = 500;
    $threshold_max = 2000;

    $coin = 'usdt';

    $dj_username = C('coin')['usdt']['zc_wallet_user'];
    $dj_password = C('coin')['usdt']['zc_wallet_passwd'];
    $dj_address = C('coin')['usdt']['zc_wallet_ip'];
    $dj_port = C('coin')['usdt']['zc_wallet_port'];


    $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 60, array(), 1);
    $json = $CoinClient->getinfo();
    var_dump($json);
    if (!isset($json['version']) || !$json['version']) {
        echo  "ERROR {$coin}  连接失败 \n";

        exit;
    }

    $usdt_coin_base = C('USDT_WALLET_COINBASE');


    $result = $CoinClient->walletpassphrase("{$password}", 32400);

    if($result != 'nodata'){
        echo "ERROR {$coin} 解锁失败 \n";
        exit;
    }


    $accounts = $CoinClient->listaddressgroupings();

    foreach($accounts as $sub_accounts){
        foreach($sub_accounts as $account){
            $address = $account[0];
            $btc_balance = $account[1];

            echo "address:{$address} \n";
            if($address == $usdt_coin_base){
                continue;
            }
            $usdt_balance = $CoinClient->omni_getbalance($address, 31);
            $usdt_balance = floatval($usdt_balance['balance']);

            echo "{$address} usdt balance {$usdt_balance} \n";

            echo "{$address} btc balance {$btc_balance} \n";

            if($btc_balance > 0.001){
                $exists = M('CoinTransfer')->where("`from`='{$address}' and `to`='{$usdt_coin_base}' and coin='btc' and status='0'")->getField("id");
                if(!$exists){
                    $result = send_btc($usdt_coin_base, $btc_balance);
                    var_dump($result);
                    if($result['code']){
                        $new_transfer = array(
                            'from' => $address,
                            'to' => $usdt_coin_base,
                            'coin' => "btc",
                            'value' => $btc_balance,
                            'txhash' => $result['txhash'],
                            'remark' => "回调地址转主账号",
                            'addtime' => time(),
                            'gas_price' => 0,
                        );
                        M('CoinTransfer')->add($new_transfer);
                    }else{
                        echo "ERROR BTC 转出失败 \n";
                    }
                }
            }

            //continue;
            if($usdt_balance >= $threshold_min){

                $hour1_ago = strtotime("-2 hours");
                if($btc_balance < 0.0002){
                    $exists = M('CoinTransfer')->where("`from`='{$usdt_coin_base}' and `to`='{$address}' and coin='btc' and status='0'")->getField("id");
                    if(!$exists){
                        $result = send_btc($address, 0.0002);
                        var_dump($result);
                        if($result['code']){
                            $new_transfer = array(
                                'from' => $usdt_coin_base,
                                'to' => $address,
                                'coin' => "btc",
                                'value' => 0.0002,
                                'txhash' => $result['txhash'],
                                'remark' => "{$coin}转主账号,BTC不足,先转其手续费",
                                'addtime' => time(),
                                'gas_price' => 0,
                            );
                            M('CoinTransfer')->add($new_transfer);
                        }else{
                            echo "ERROR BTC 转出失败 \n";
                        }
                    }

                }else{
                    echo "{$address} start to sum2main \n";
                    $exists = M('CoinTransfer')->where("`from`='{$address}' and `to`='{$usdt_coin_base}' and coin='usdt' and status='0'")->getField("id");
                    if(!$exists){
                        $result = send_usdt($address, $usdt_coin_base, $usdt_balance);
                        var_dump($result);
                        if($result['code']){
                            $new_transfer = array(
                                'from' => $address,
                                'to' => $usdt_coin_base,
                                'coin' => $coin,
                                'value' => $usdt_balance,
                                'txhash' => $result['txhash'],
                                'remark' => "{$coin}转主账号",
                                'addtime' => time(),
                                'gas_price' => 0,
                            );
                            M('CoinTransfer')->add($new_transfer);
                        }else{
                            echo "ERROR {$coin} 转出失败 \n";
                        }
                    }
                }


            }
        }
    }

    echo "{$coin} sum2main completed";
}