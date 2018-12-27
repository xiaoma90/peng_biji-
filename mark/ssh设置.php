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
执行生成公钥和私钥的命令：ssh-keygen -t rsa 
并按回车3下（为什么按三下，是因为有提示你是否需要设置密码，如果设置了每次使用Git都会用到密码，一般都是直接不写为空，直接回车就好了）。
会在一个文件夹里面生成一个私钥 id_rsa和一个公钥id_rsa.pub。
执行查看公钥的命令：cat ~/.ssh/id_rsa.pub  

EOT;
