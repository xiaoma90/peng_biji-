<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2018/12/18
 * Time: 14:45
 */
<<<upg
1：备份当前数据库数据、 最好是导成 SQL 文件
2：备份 PhpStudy 下的 MySQL 文件夹、以防升级失败、还可以使用旧版本的数据库
3：下载MySQL5.7、解压、然后放在 PhpStudy 下的 MySQL 文件夹下
地址：https://dev.mysql.com/downloads/file/?id=467269
4：复制一份my-default.ini，改名为my.ini、打开my.ini加上：
basedir="E:/phpStudy/MySQL/"    
datadir="E:/phpStudy/MySQL/data/"
这两项，这两个地址都改成自己对应的phpstudy里的mysql目录和数据库目录。
5：在系统path中添加：  ;E:\phpstudy\MySQL\bin  //这里的地址根据自己的情况写
6：然后以管理员的身份运行cmd、进入MySQL目录、如图所示表示成功、执行如下:
> mysqld --initialize
> mysqld -install

7：重新启动 phpstudy即可、
upg;


<<<eot
遇到问题
登录非匿名账户提示’Plugin ‘******’ is not loaded’.

解决办法
1.开启无密码登录 
修改mysql.cnf 在 [mysqld]下添加skip-grant-tables

2 .sudo service mysqld restart 重启mysql服务.
3. 现在就可以登录了。登录root账户执行以下语句.

use mysql;
update user set authentication_string=PASSWORD("") where User='root';
update user set plugin="mysql_native_password";
flush privileges;
quit;
1
2
3
4
5
4.将my.cnf修改回来 
5.再次重启mysql，完毕

eot;

