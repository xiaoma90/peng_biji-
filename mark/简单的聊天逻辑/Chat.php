<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2019/3/25
 * Time: 9:38
 */

class Chat
{
    /**
     * 点击聊天
     * 判断是不是第一次聊天，如果是会在主表生成一条记录返回聊天主表id，
     * 并在聊天列表表分别插入两条记录，如果不是第一次聊天进入下一步
     */
    public function judgeChat()
    {
        if (!request()->isPost()) {
            return ajaxMessages(305, "请稍后重试");
        }
        $id = request()->param('id', '');
        $chat = db('ns_chat')->where(['user_id'=>$this->uid,'another_id'=>$id])->find();
        if(empty($chat)){
            Db::startTrans();
            try{
                $time = time();
                $cid = db('ns_chat')->insertGetId(['user_id'=>$this->uid,'another_id'=>$id,'create_time'=>$time]);

                db('ns_chat_list')->insert(['chat_id'=>$cid,'user_id'=>$this->uid,'another_id'=>$id,'create_time'=>$time]);
                db('ns_chat_list')->insert(['chat_id'=>$cid,'user_id'=>$id,'another_id'=>$this->uid,'create_time'=>$time]);
                Db::commit();
                return ajaxMessages(200,'已添加',$cid);
            }catch (\Exception $e){
                Db::rollback();
                return ajaxMessages(210,$e->getMessage());
            }

        }else{
            return ajaxMessages(200,'ok',$chat['id']);
        }
    }

    /**
     * 进入聊天框
     * 将用户在此对话的在线状态改为在线
     */
    public function inChat()
    {
        if (!request()->isPost()) {
            return ajaxMessages(305, "请稍后重试");
        }
        $id = request()->param('id', '');
        if(!$id){
            return ajaxMessages(210, "参数错误");
        }
        db('ns_chat_list')->where(['chat_id'=>$id,'user_id'=>$this->uid])->update(['is_online'=>1,'unread'=>0]);
        db('ns_chat_list')->where(['chat_id'=>['<>',$id],'user_id'=>$this->uid])->update(['is_online'=>0]);//其他接受离线消息
        return ajaxMessages(200,'ok');
    }
    /**
     * 发送聊天信息
     */
    public function addChat()
    {
        if (!request()->isPost()) {
            return ajaxMessages(305, "请稍后重试");
        }
        $param = request()->param();
        $id =  $param['id'];
        $type =  $param['msg_type'];
        $msg  = $param['msg'];
//        $msg  = '斯科拉放假吗';
        if(!$id || !$type || !$msg){
            return ajaxMessages(202,'参数错误');
        }
        $chat = db('ns_chat')->where('id',$id)->find();
        if(!$chat){
            return ajaxMessages(203,'聊天不存在');
        }
        /**
         * 先判断对方是否在线，不在线的话对方未读数+1
         */
        $chat_list = db('ns_chat_list')->where(['chat_id'=>$id,'another_id'=>$this->uid])->find();
        if($chat_list['is_online'] != 1){
            db('ns_chat_list')->where(['chat_id'=>$id,'another_id'=>$this->uid])->setInc('unread');
        }
        /**
         * 将上一条最后一条消息状态改为否
         */
        db('ns_chat_info')->where('chat_id',$id)->update(['is_latest'=>2]);
        /**
         * 往聊天详情表插入聊天信息数据
         */
        $res = db('ns_chat_info')->insert(['chat_id' => $id,'user_id'=> $this->uid,'type'=> $type,'content' => $msg,'create_time'=>time()]);
        if($res){
            return ajaxMessages(200,'发送成功');
        }else{
            return ajaxMessages(202,'发送失败');
        }
    }
    /**
     * 删除聊天列表
     * 将该用户的聊天列表删除状态改为删除
     */
    public function delChat()
    {
        if (!request()->isPost()) {
            return ajaxMessages(305, "请稍后重试");
        }
        $id = input('id/d');
        db('ns_chat_list')->where(['chat_id'=>$id,'user_id'=>$this->uid])->update(['status'=>2]);
        return ajaxMessages(200,'操作成功');
    }

    /**
     * 聊天列表
     */
    public function chatList()
    {
        $data = db('ns_chat_list')->where(['user_id'=>$this->uid])->select();
        $return = [];
        foreach ($data as $k=>$v){
            $user = db('sys_user')->where(['uid'=>$v['another_id']])->find();
            $chat = db('ns_chat_info')->where(['chat_id'=>$v['chat_id'],'is_latest'=>1])->find();
            $return[] = [
                'id' => $v['chat_id'],
                'name' => $user['user_name'],
                'create_time' => $chat['create_time']?date('Y-m-d',$chat['create_time']):date('Y-m-d',$v['create_time']),
                'msg' => $chat['content']?:'',
                'type' => $chat['type']?:'',
                'unread' => $v['unread'],
                'avator' => $user['user_headimg']?:'',
            ];
        }
        return ajaxMessages(200,'ok',$return);
    }

    /**
     * 聊天详情
     */
    public function chatInfo()
    {
        if (!request()->isPost()) {
            return ajaxMessages(305, "请稍后重试");
        }
        $id = request()->param('id',1);
        $chat = db('ns_chat')->where(['id'=>$id])->find();
        $data = db('ns_chat_info')->where(['chat_id'=>$id])->select();
        $shop_id = db('sys_user')->where(['uid'=>['in',[$chat['user_id'],$chat['another_id']]]])->value('instance_id');
//        halt("select *from ns_shop where 'shop_id'={$shop_id} and shop_state=1");
        $shop = Db::query("select *from ns_shop where shop_id={$shop_id} and shop_state=1");
        if($shop){
            $shop = $shop[0];
            $return = [
                'title' => $shop['shop_name'],
                'shop_id' => $shop['shop_id'],
                'user_id' => $chat['user_id'],
                'another_id' => $chat['another_id'],
                'chat' => []
            ];
            foreach ($data as $k=>$v){
                $user = db('sys_user')->where(['uid'=>$v['user_id']])->find();
                $return['chat'][] = [
                    'user_id' => $v['user_id'],
                    'avator' => $user['user_headimg'],
                    'type' => $v['type'],
                    'content' => $v['content'],
                ];
            }
            return ajaxMessages(200,'ok',$return);

        }
        return ajaxMessages(200,'ok',[]);
    }

    /**
     * 平台消息
     */
    public function platformMessage()
    {
        /**
         * 首次打开生成列表
         */
        $id = 10;
        if($this->uid == $id){
            return ajaxMessages(210,'你闲的..');
        }
        $chat = db('ns_chat')->where(['user_id'=>$this->uid,'another_id'=>$id])->find();
        if(empty($chat)){
            Db::startTrans();
            try{
                $time = time();
                $cid = db('ns_chat')->insertGetId(['user_id'=>$this->uid,'another_id'=>$id,'create_time'=>$time]);
                db('ns_chat_list')->insert(['chat_id'=>$cid,'user_id'=>$this->uid,'another_id'=>$id,'create_time'=>$time]);
                db('ns_chat_list')->insert(['chat_id'=>$cid,'user_id'=>$id,'another_id'=>$this->uid,'create_time'=>$time]);
                Db::commit();
            }catch (\Exception $e){
                Db::rollback();
                return ajaxMessages(210,$e->getMessage());
            }
        }
        db('ns_chat_list')->where(['chat_id'=>$cid,'user_id'=>$this->uid])->update(['is_online'=>1,'unread'=>0]);
        db('ns_chat_list')->where(['chat_id'=>['<>',$cid],'user_id'=>$this->uid])->update(['is_online'=>0]);//其他接受离线消息
        $data = db('ns_chat_info')->where(['chat_id'=>$cid])->select();
        $shop_id = db('sys_user')->where(['uid'=>['in',[$chat['user_id'],$chat['another_id']]]])->value('instance_id');
        $shop = Db::query("select *from ns_shop where shop_id={$shop_id} and shop_state=1");
        if($shop){
            $shop = $shop[0];
            $return = [
                'title' => $shop['shop_name'],
                'shop_id' => $shop['shop_id'],
                'user_id' => $chat['user_id'],
                'another_id' => $chat['another_id'],
                'chat' => []
            ];
            foreach ($data as $k=>$v){
                $user = db('sys_user')->where(['uid'=>$v['user_id']])->find();
                $return['chat'][] = [
                    'user_id' => $v['user_id'],
                    'avator' => $user['user_headimg'],
                    'type' => $v['type'],
                    'content' => $v['content'],
                ];
            }
            return ajaxMessages(200,'ok',$return);

        }
        return ajaxMessages(200,'ok',[]);
    }
}
