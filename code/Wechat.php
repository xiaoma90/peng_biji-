<?php
namespace app\home\controller;

use wechats\Wechat as Wechats;
use filesh\File;
use think\Controller;

class Wechat extends Controller {
	#微信验证
	public function token(){
        file_put_contents('./log.txt',10);
        # 监听关注事件
        Wechats::addEvent('subscribe',function($result) {
            // file_put_contents('./log.txt',20);
            file_put_contents('./log.txt', json_encode($result));
            if(db('users') -> where(['openid'=>$result['FromUserName']]) -> find()){
                return true;
            }
            # 获取用户信息
            $userinfo = Wechats::get_openid_user_info($result['FromUserName']);
            # 用户数组
            $data = [];
            # 判断是否存在票据
            if($result['Ticket']!=''){
                # 获取上级信息
                if($puser = db('users') -> where(['qrcode'=>$result['Ticket']]) -> find()){
                    // 设置上级id
                    $data['pid'] = $puser['id'];
                }
            }
            # 用户唯一标识
            $data['openid'] = $userinfo['openid'];
            # 性别 1=男 2=女性 0=未设置
            $data['sex'] = $userinfo['sex'];
            # 用户昵称
            $data['nickname'] = $userinfo['nickname'];
            $data['weixin'] = $userinfo['nickname'];
            # 下载头像到本地
            // File::_download($userinfo['headimgurl'],ROOT_PATH.'public/headimgurl/',$userinfo['openid'].'.jpg');
            # 头像
            $data['headimgurl'] = $userinfo['headimgurl'];
            # 创建时间
            $data['created_at'] = time();
            # 最后更新时间
            $data['updated_at'] = $data['created_at'];
            # 插入用户数据
            $id = db('users')->insertGetId($data);
            if($id){
                # 获取带参数二维码
                $qrcode = Wechats::get_Qrcode($id);
                # 获取Ticked
                $qrcode = substr($qrcode,51);
                # 下载二维码
                File::_download('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$qrcode,ROOT_PATH.'public/qrcode/',$qrcode.'.jpg');
                # 修改用户Ticked
                $jg = db('users')->where(['id'=>$id])->update(['qrcode'=>$qrcode]);
            }
            $text = "欢迎关注畅享商城";
            # 输出欢迎关注
            exit('<xml>
                <ToUserName><![CDATA['.$result['FromUserName'].']]></ToUserName>
                <FromUserName><![CDATA['.$result['ToUserName'].']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA['.$text.']]></Content>
                </xml>');
        });
	}

	//微信导航栏
    public function menu(){
        $data = [
            ['name'=>'商城','event'=>'view','val'=>DOMIAN.'/home/login/wechat_login'],
            ['name'=>'APP下载','event'=>'view','val'=>FRDOMAIN.'/app/changxiang.apk'],
            ['name'=>'更多','two'=>
                [
                    ['name'=>'最新公告','event'=>'view','val'=>FRDOMAIN.'/announce.html'],
                    ['name'=>'新手指南','event'=>'view','val'=>FRDOMAIN.'/newbieGuide.html'],
                ]
            ]

        ];
        dump(Wechats::menu_create($data));
    }

	
}