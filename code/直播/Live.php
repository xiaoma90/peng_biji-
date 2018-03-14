<?php

namespace app\backend\controller;

use think\Controller;
use app\backend\controller\Livedirt;
class Live {
    const appKey = 'iey6u5sxi16asnci';
    const appSecret = 'AIyQo4Y9HRqbpmzy';
    const ImpromptuUrl = 'http://api.quklive.com/cloud/services/impromptu/create';   //即兴直播
    const ActivitalUrl = 'http://api.quklive.com/cloud/services/activity/create';   //添加活动
    const DelActUrl = 'http://api.quklive.com/cloud/services/activity/delete';   //删除活动
    const ActivityInfoUrl = 'http://api.quklive.com/cloud/services/activity/info';   //活动信息
    const ModifyActInfoUrl = 'http://api.quklive.com/cloud/services/activity/update';   //修改活动信息
    const CheckActIsLiveUrl = 'http://api.quklive.com/cloud/services/activity/isLive';   //检查用户是否在直播中
    const StartedActLivingUrl = 'http://api.quklive.com/cloud/services/activity/getStartedActivities';   //查询主账号下所有在直播的活动
    public function addImpromptu(){             //增加即兴直播
        $course = new Livedirt();
        $configData = $course->getConfig();
        $impromptuData['name'] = '直播名称';
        $impromptuData['secretLinkAble'] = 1;
        $impromptuData['appKey'] = self::appKey;
        $impromptuData['nonce'] = $configData['nonce'];
        $impromptuData['signature'] = $configData['signature'];
        $data = json_decode(http(self::ImpromptuUrl,json_encode($impromptuData),'post'),true);
        var_dump($data);
    }
    public function addActivity(){           //增加活动
        $course = new Livedirt();
        $configData = $course->getConfig();
        $activityData['name'] = '活动名称';
        $activityData['startTime'] = '2017-06-24 19:20:00';
        $activityData['endTime'] = '2017-06-24 20:00:00';
        $activityData['expireTime'] = '2017-06-26 19:30:00';
        $activityData['memberName'] = '贾建超';
        $activityData['disableRecord'] = 0;
        $activityData['appKey'] = self::appKey;
        $activityData['nonce'] = $configData['nonce'];
        $activityData['signature'] = $configData['signature'];
        $activityData['secretLinkAble'] = 1;
        $data = http(self::ActivitalUrl,json_encode($activityData),'POST');
        var_dump($data);
    }
    public function delActivity(){          //删除活动
        $course = new Livedirt();
        $configData = $course->getConfig();
        $delData['id'] = '576684106841834935108';
        $delData['appKey'] = self::appKey;
        $delData['nonce'] = $configData['nonce'];
        $delData['signature'] = $configData['signature'];
        $data = http(self::DelActUrl,json_encode($delData),'POST');
        dump($data);
    }
    public function activityInfo(){          //获取直播信息
        $course = new Livedirt();
        $configData = $course->getConfig();
        $actInfoData['id'] = '576684106841834935108';
        $actInfoData['appKey'] = self::appKey;
        $actInfoData['nonce'] = $configData['nonce'];
        $actInfoData['signature'] = $configData['signature'];
        $data = http(self::ActivityInfoUrl,json_encode($actInfoData),'post');
        dump($data);
    }
    public function modifyActInfo(){        //修改直播信息
        $course = new Livedirt();
        $configData = $course->getConfig();
        $modifyData['id'] = '576684106841834935108';
        $modifyData['name'] = '活动名称';
        $modifyData['startTime'] = '2017-06-26 10:30:00';
        $modifyData['endTime'] = '2017-06-26 11:00:00';
        $modifyData['expireTime'] = '2017-06-28 11:00:00';
        $modifyData['disableRecord'] = 0;
        $modifyData['secretLinkAble'] = 1;
        $modifyData['appKey'] = self::appKey;
        $modifyData['nonce'] = $configData['nonce'];
        $modifyData['signature'] = $configData['signature'];
        $data = http(self::ModifyActInfoUrl,json_encode($modifyData),'post');
        dump($data);
    }
    public function checkActIsLive(){        //检查用户是否在直播中
        $course = new Livedirt();
        $configData = $course->getConfig();
        $checkData['liveIdList'] = array(
            '576684106841834935108',
            '576684106841834935104'
        );
        $checkData['appKey'] = self::appKey;
        $checkData['nonce'] = $configData['nonce'];
        $checkData['signature'] = $configData['signature'];
        $data = http(self::CheckActIsLiveUrl,json_encode($checkData),'post');
        dump($data);
    }
    public function getAllActLinving(){         //获取所有主账号下的正在直播的活动
        $course = new Livedirt();
        $configData = $course->getConfig();
        $getAllData['page'] = 1;
        $getAllData['appKey'] = self::appKey;
        $getAllData['nonce'] = $configData['nonce'];
        $getAllData['signature'] = $configData['signature'];
        $data = http(self::StartedActLivingUrl,json_encode($getAllData),'post');
        dump($data);
    }
}
