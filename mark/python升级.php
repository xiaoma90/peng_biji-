<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2019/1/9
 * Time: 11:06
 */
<<<EOT
首先下载源tar包

可利用linux自带下载工具wget下载，如下所示：

wget https://www.python.org/ftp/python/3.7.2/Python-3.7.2.tgz

 

下载完成后到下载目录下，解压

tar -xzvf Python-3.3.0.tgz

 

进入解压缩后的文件夹

cd Python-3.3.0　　

 

在编译前先在/usr/local建一个文件夹python3（作为python的安装路径，以免覆盖老的版本）

mkdir /usr/local/python3

　　

开始编译安装

./configure --prefix=/usr/local/python3

make

make install

 

此时没有覆盖老版本，再将原来/usr/bin/python链接改为别的名字

mv /usr/bin/python /usr/bin/python_old2

　　

再建立新版本python的链接

ln -s /usr/local/python3/bin/python3 /usr/bin/python

　　

这个时候输入

python -V

　　

就会显示出python的新版本信息

[idolaoxu@localhost home]# python -V

Python 3.3.0

 

PS：如果不建立新安装路径python3，而是直接默认安装，则安装后的新python应该会覆盖linux下自带的老版本，也有可能不覆盖，具体看安装过程了，

这个大家可以自己试验下，当然如果还想保留原来的版本，那么这种方法最好不过了。

 

  

最后扩充下，

这种方法虽然能安装成功，但是它带来了新的问题，比如yum不能正常用了

修改/usr/bin/yum的第一行为：

#!/usr/bin/python_old2

就可以了  
EOT;
