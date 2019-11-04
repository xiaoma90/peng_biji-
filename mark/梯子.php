<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2019/1/5
 * Time: 14:40
 */
<<<EOT
1,先买一个国外的服务器
2,

此脚本是用于下载配置SSR需要的基础资源。
wget --no-check-certificate https://raw.githubusercontent.com/teddysun/shadowsocks_install/master/shadowsocks-all.sh
此脚本是打开文件权限，并不会在窗口显示任何返回值。所以只要像下方截图这样即可。
chmod +x shadowsocks-all.sh
此脚本用于配置并安装SSR。
./shadowsocks-all.sh 2>&1 | tee shadowsocks-all.log
运行后出现下图的信息，这里我们选择“2”的 ShowdowsocksR选项，回车运行。
下一个就需要大家配置自己的密码，像下图这样，如果不输入则是默认的。输入完密码后，回车运行。（记得密码备份到文档中）

下一条就需要大家选择配置的端口，一般会使用自动分配的端口号（不要使用22、433之类的常见端口）这里提示的是18485端口，我们就使用提示的端口号。
下一条需要大家选择加密的方式，这里我们选择”2″即aes-256-cfb模式（这个也是最常用的模式）回车运行。
协议部分，我们选择“1”的Origin原生模式。回车继续运行。
Obfs混淆模式这里，我们选择“1”的Plain模式，回车运行。
最后，再次回车，让服务器自动开始安装SSR梯子所需的资源。（大约需要等待1-5分钟左右，期间不要关闭窗口）
当出现如下信息就代表安装成功，SSR配置已经完成。

EOT;

