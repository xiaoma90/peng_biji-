<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2018/12/27
 * Time: 9:10
 */

<<<EOT
打开ssh bash
设置用户名 用户邮箱
git config --global user.name “用户名”

git config --global user.email “邮箱”
执行生成公钥和私钥的命令：ssh-keygen -t rsa -C ''
并按回车3下（为什么按三下，是因为有提示你是否需要设置密码，如果设置了每次使用Git都会用到密码，一般都是直接不写为空，直接回车就好了）。
会在一个文件夹里面生成一个私钥 id_rsa和一个公钥id_rsa.pub。
执行查看公钥的命令：cat ~/.ssh/id_rsa.pub  


git init
git remote add origin https://github.com/******/web // 第一次的时候需要
git add .
git commit -m "Initial commit"
git push -u origin master // 第一次的时候需要 
// 需要输入账号密码的时候，输入你在git申请的账号和设置过的密码就OK
 
 
cd /Users/tuoge/Desktop/iOS/bluetoothLock  // 记得要cd到所有上传的文件目录下
git status // 查看文件状态
git add .
git commit -m "修改" 
git push // 以后就push一下就OK了

————————————————
版权声明：本文为CSDN博主「乔布斯狂热追随者」的原创文章，遵循 CC 4.0 BY-SA 版权协议，转载请附上原文出处链接及本声明。
原文链接：https://blog.csdn.net/jwheat/article/details/80751740
EOT;
