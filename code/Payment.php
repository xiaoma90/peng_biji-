<?php
namespace app\home\controller;

use alipayment\Alipay;
use app\backend\model\Config;
use app\home\controller\Row;
use Payment\NotifyContext;
use think\Controller;
use think\Db;
use wechats\Wechat;
use wechats\Wxapp;

class Payment extends Controller
{
    const RECHARGE = 'recharge'; //充值升级表
    const USER     = 'users'; //用户表
    const ACCOUNT  = 'account'; //记录表
    const ORDER    = 'order'; //订单表
    // ---------------------------------------------------判断是否微信---------------------------------------------------------------//
    public function wxOrWeb()
    {
        if (is_weixin()) {
            return json(['status' => 0, 'msg' => '微信']);
        } else {
            return json(['status' => 1, 'msg' => '浏览器']);
        }
    }
// ---------------------------------------------------订单处理---------------------------------------------------------------//
    public function orderDeal()
    {
        $id    = input('id');
        $uid   = session('home.user')['id'];
        $class = input('class');
        $user  = db('users')->where(['id' => $uid])->find();
        $order = db('order')->where(['id' => $id, 'status' => 1])->find();
        if (empty(input('city'))) {
            return json(['status' => 2, 'message' => '请完善收货地址']);
        } else {
            if ($class != 'wx_app') {
                if (empty(input('uid')) || input('uid') != $uid) {
                    return json(['status' => 2, 'message' => '收货地址异常']);
                }
            }

        }
        if (empty($order)) {
            return json(['status' => 3, 'message' => '该订单信息不正确']);
        } else {
            if ($class != 'wx_app') {
                if ($order['uid'] != $uid) {
                    return json(['status' => 3, 'message' => '请确认用户信息']);
                }
            }

        }
        if (empty($user['openid']) && input('class') == 'wx' && is_weixin()) {
            return json(['status' => 4, 'message' => '需绑定微信才能进行微信支付']);
        }
        $money = $order['price'];
        // $money = 0.01;

        $upOrder = [
            'city'           => input('city'),
            'address_detail' => !empty(input('description')) ? input('description') : '',
            'address_phone'  => !empty(input('phone')) ? input('phone') : '',
            'address_name'   => !empty(input('name')) ? input('name') : '',
            'payment'        => input('payment'),
            'updated_at'     => time(),
            'score'          => $money,
        ];

        //2微信 1支付宝
        $re  = db('order')->where(['id' => $id])->update($upOrder);
        $res = db('order')->where(['id' => $id])->find();

        if ($class == 'ali') {
            // $class = 'ali';
            $body = '订单支付';
            // return json(['status'=>1,'message'=>'提交成功','data'=>$this->payRoute($class,$res['order_sn'],$res['price'],$body)]);
            return json(['status' => 1, 'message' => '提交成功', 'data' => $this->payRoute($class, $res['order_sn'], $money, $body)]);
        } elseif ($class == 'wx_app') {
            $body = '订单支付';
            // return json_decode(json_encode(['status' => 1, 'message' => '提交成功', 'data' => $this->payRoute($class, $res['order_sn'], $money, $body)]));
            return json(['status' => 1, 'message' => '提交成功', 'data' => $this->payRoute($class, $res['order_sn'], $money, $body)]);
        } else {
            if (is_weixin()) {
                $cs = self::wxPay($order['order_sn'], $money);
                return json(['status' => 1, 'message' => '提交成功', 'data' => $cs]);
            } else {
                return json(['status' => 2, 'message' => '请在微信公众号中打开']);
            }
            // dump($cs);
        }

    }
// -------------------------------------------------支付路由---------------------------------------------------------------//
    private function payRoute($type, $order_sn, $money, $body)
    {
        #支付宝支付、微信支付、支付宝app支付、微信app支付、支付宝app支付接口对接、微信app支付接口对接、
        switch ($type) {
            case 'ali':
                return $this->aliPay($order_sn, $money, $body);
                break;
            case 'ali_app':
                return $this->aliAppPay($order_sn, $money, $body);
                break;
            case 'ali_app_api':
                return $this->aliAppPay($order_sn, $money, $body);
                break;
            case 'wx':
                return $this->wxPay($order_sn, $money, $body);
                break;
            case 'wx_app':
                return $this->wxAppPay($order_sn, $money, $body);
                break;
            case 'wx_app_api':
                return $this->wxAppPay($order_sn, $money, $body, 'api');
                break;
        }
    }
// -------------------------------------------------------支付宝支付-------------------------------------------------------//
    private function aliPay($order_sn, $money, $body = '支付宝支付')
    {
        $data = [
            'order_no'        => $order_sn,
            // 'amount'        => 1,
            'amount'          => $money,
            'body'            => $body,
            'subject'         => $body,
            'timeout_express' => (time() + 1800),
            'return_param'    => 'buy',
        ];
        $res = Alipay::create($data); //提交订单
        if (is_weixin()) {
            $data = FRDOMAIN . "/ydy.html?sss=" . $res;
            return $data;
            // header("location:".DOMAIN."/home/alipays/ydy.html?sss=".$res);
        } else {
            // header("location:".$res);
            return $res;
        }
    }
// ----------------------------------------------------支付宝app支付-----------------------------------------------------//
    private function aliAppPay($order_sn, $money, $body = '支付宝支付')
    {
        $data = [
            'order_no'        => $order_sn,
            // 'amount'        => 0.01,
            'amount'          => $money,
            'body'            => $body,
            'subject'         => $body,
            'timeout_express' => (time() + 1800),
            'return_param'    => 'buy',
        ];
        $res = Alipay::create($data, 'ali_app');
        return $res;
    }
// ----------------------------------------------------微信支付--------------------------------------------------------------//
    private function wxPay($order_sn, $money, $body = '微信支付')
    {
        # 定义下单内容
        $data = [
            'body'         => $body,
            // 'total_fee'=>100,
            'total_fee'    => ($money * 100),
            'openid'       => session('home.user')['openid'],
            'trade_type'   => 'JSAPI',
            'out_trade_no' => $order_sn,
            'notify_url'   => config('Wechat')['notify_url'],
        ];
        # 获取JSAPI
        $jsapi = Wechat::get_jsapi_config(['chooseWXPay'], false, true);

        # 获取微信支付配置
        $payConfig = Wechat::ChooseWXPay($data, false);
        // dump($payConfig);exit;
        return json_decode($payConfig);
    }
// ------------------------------------------微信app支付---------------------------------------------------------------//
    public function wxAppPay($order_sn, $money, $body = '支付宝支付', $type = false)
    {
        $app = new Wxapp();
        #微信app支付对接接口
        if ($type == 'api') {
            return $app->sendRequest1($order_sn, $money);
        } else {
#微信app支付
            return $app->sendRequest($order_sn, $money);
        }
    }
// ------------------------------------------支付宝回调---------------------------------------------------------------//
    public function alipayNotify()
    {
        $result = new NotifyContext();
        $data   = [
            'app_id'          => config('Alipay')['app_id'],
            'notify_url'      => config('Alipay')['notify_url'],
            'return_url'      => config('Alipay')['return_url'],
            'sign_type'       => config('Alipay')['sign_type'],
            'ali_public_key'  => config('Alipay')['ali_public_key'],
            'rsa_private_key' => config('Alipay')['rsa_private_key'],
        ];
        # 校验信息
        $result->initNotify('ali_charge', $data);
        # 接受返回信息
        $information = $result->getNotifyData();
        if ($information['trade_status'] == 'TRADE_SUCCESS') {
            $pay_order = (String) $information['out_trade_no'];
            $total_fee = $information['total_amount'];
            $res       = dealWith($pay_order, $total_fee);
            if ($res == 'success') {

                echo "success";exit;
            }
            echo "fail";exit;
        } else {
            echo "fail";exit;
        }
    }

// ------------------------------------------微信回调-----------------------------------------------------------------//
    public function native()
    {
        # 监听回调通知
        Wechat::notitfy(function ($notify) {
            $pay_order_num = (String) $notify['out_trade_no'];
            $total_fee     = $notify['total_fee'];

            $res = dealWith($pay_order_num, $total_fee / 100);

            if ($res == 'success') {
                echo "success";exit;
            }
            echo "fail";exit;
        });
    }
// ------------------------------------------支付宝同步跳转----------------------------------------------------------------//
    public function redir()
    {
        header("location:http://www.szcxdzsw.com/pay_success.html");exit;
        // dump('充值完成');
    }

    public function test()
    {
        $row = new Row();
        $row->rowApi($orders['uid'], $gao_taocan);

    }

// -------------------------------------------------支付宝引导页判断-------------------------------------------------------//
    public function ydy()
    {
        if (request()->isAjax()) {
            if (is_weixin()) {
                return json(['status' => 0, 'msg' => '微信']);
            } else {
                return json(['status' => 1, 'msg' => '浏览器']);
            }
        }
        return view('html/ydy');
    }
    public function fenxiao($orderid = '')
    {
        if (!empty($orderid)) {
            //=============================算法开始=====================================//
            $config = [
                'distribution1' => db('config')->where(['name' => 'distribution1'])->value('value'),
                'distribution2' => db('config')->where(['name' => 'distribution2'])->value('value'),
                'distribution3' => db('config')->where(['name' => 'distribution3'])->value('value'),
                'fuxiao1'       => db('config')->where(['name' => 'fuxiao1'])->value('value'),
                'fuxiao2'       => db('config')->where(['name' => 'fuxiao2'])->value('value'),
                'fuxiao3'       => db('config')->where(['name' => 'fuxiao3'])->value('value'),
            ];
            $orders = db('order')->where(['id' => $orderid])->find();
            if ($orders['payment']) {
                $user         = db('users')->where(['id' => $orders['uid']])->find();
                $price        = 0;
                $order_detail = db('order_detail')->where(['oid' => $orderid])->select();
                $taocan1      = $taocan2      = $taocan3      = 0;
                foreach ($order_detail as $k => $v) {
                    if ($v['gname'] == 'V1尊享优惠套餐' || $v['gname'] == 'V2尊享优惠套餐' || $v['gname'] == 'V3尊享优惠套餐') {
                        $price += $v['gprice'] * $v['g_num'];
                    }
                    if ($v['gname'] == 'V1尊享优惠套餐') {
                        $taocan1 += $v['gprice'] * $v['g_num'];
                    } elseif ($v['gname'] == 'V2尊享优惠套餐') {
                        $taocan2 += $v['gprice'] * $v['g_num'];
                    } elseif ($v['gname'] == 'V3尊享优惠套餐') {
                        $taocan3 += $v['gprice'] * $v['g_num'];
                    }
                }

                $is_one = db('static_order')->where(['uid' => $user['id']])->find();
                if (!$is_one) {
                    db('order')->where(['id' => $orderid])->update(['is_new' => 1]);
                }
                // file_put_contents('./1.txt', $price);
                //====================进行分销====================//
                $p1 = db('users')->where(['id' => $user['pid']])->find();
                if ($p1) {
                    if ($price > 0 && $p1['class'] > 1) {
                        // $bili = $p1['class'] * 3 - 3;
                        if (!$is_one) {
                            $fan = round($price * $config['distribution1'] / 100, 2);
                            add_account($p1['id'], $fan, '下级用户' . $user['nickname'] . '的分销奖金', 3, $user['id']);
                        } else {
                            $fan = round($price * $config['fuxiao1'] / 100, 2);
                            add_account($p1['id'], $fan, '下级用户' . $user['nickname'] . '的复消分销奖金', 3, $user['id']);
                        }
                    }
                    $p2 = db('users')->where(['id' => $p1['pid']])->find();
                    if ($p2) {
                        if ($price > 0 && $p2['class'] > 1) {
                            // $bili = $p2['class'] * 3 - 2;
                            if (!$is_one) {
                                $fan = round($price * $config['distribution2'] / 100, 2);
                                add_account($p2['id'], $fan, '下级用户' . $user['nickname'] . '的分销奖金', 3, $user['id']);
                            } else {
                                $fan = round($price * $config['fuxiao2'] / 100, 2);
                                add_account($p2['id'], $fan, '下级用户' . $user['nickname'] . '的复消分销奖金', 3, $user['id']);
                            }
                        }
                        $p3 = db('users')->where(['id' => $p2['pid']])->find();
                        if ($p3) {
                            if ($price > 0 && $p2['class'] > 1) {
                                if (!$is_one) {
                                    $fan = round($price * $config['distribution3'] / 100, 2);
                                    add_account($p3['id'], $fan, '下级用户' . $user['nickname'] . '的分销奖金', 3, $user['id']);
                                } else {
                                    $fan = round($price * $config['fuxiao3'] / 100, 2);
                                    add_account($p3['id'], $fan, '下级用户' . $user['nickname'] . '的复消分销奖金', 3, $user['id']);
                                }
                            }
                        }
                    }
                }
                $gao_taocan = max(max($taocan1, $taocan2), $taocan3);
                if ($gao_taocan > 997) {
                    $row = new Row();
                    $row->rowApi($orders['uid'], $gao_taocan);
                }

                if (!$is_one) {
                    $real_money = 0;
                    if ($gao_taocan == '998') {
                        $real_money = Config::getConfigs('cash_v1');
                    } elseif ($gao_taocan == '1998') {
                        $real_money = Config::getConfigs('cash_v2');
                    } elseif ($gao_taocan == '2998') {
                        $real_money = Config::getConfigs('cash_v3');
                    }
                    if ($gao_taocan > 997) {
                        $static = [
                            'uid'        => $user['id'],
                            'order_id'   => $orderid,
                            'money'      => $gao_taocan,
                            'real_money' => $real_money,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];
                        db('static_order')->insert($static);
                        file_put_contents('fen.txt', $user['id'] . '---', FILE_APPEND);
                    }
                }

                return $price;
            }
            //==============================算法结束====================================//
        }

    }

}
// ------------------------------------------回调处理逻辑--------------------------------------------------------------------//
function dealWith($order_sn, $total_fee)
{
    // file_put_contents('1.txt',$order_sn,FILE_APPEND);
    $order = db('order')->where(['order_sn' => $order_sn])->find();
    if ($order['status'] != 1) {
        return "success";
    } else {
        $user  = db('users')->where(['id' => $order['uid']])->find();
        $class = $user['class'];
        $goods = db('order_detail')->where(['oid' => $order['id']])->select();
        foreach ($goods as $k => $v) {
            if (in_array($v['gid'], [1, 2, 3])) {
                $class = $class > ($v['gid'] + 1) ? $class : ($v['gid'] + 1);
            }
        }
        if ($class > $user['class']) {
            db('users')->where(['id' => $order['uid']])->update(['class' => $class]);
        }
        // file_put_contents('1.txt','2',FILE_APPEND);
        $res = db('order')->where(['order_sn' => $order_sn])->update(['status' => 2, 'updated_at' => time()]);
        //$res2 = add_account1($order['uid'], '-' . $order['price'], '商品消费', 1, 0);
        $pay   = new Payment();
        $price = $pay->fenxiao($order['id']);
        // Payment()->fenxiao($order['id']);

        if ($res) {
            return 'success';
        } else {
            return 'fail';
        }

    }
}
