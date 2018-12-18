<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2018/12/18
 * Time: 14:39
 */

/**
 *  linux 下切换php 的版本
 *  主要应用 php-cli模式
 *  #ln -s /usr/local/php-5.5/bin/php(你想要换成的php版本的路径) /usr/sbin/php（最后一个php可以换成你自己喜欢的名字，最好php吧）
 * 第二步：export命令将软连接加到PATH路径中
 * #export PATH="$PATH:/usr/sbin/php"（将上面你准备好的连接加进去就可以了，中间：冒号别漏了，用来做分割的）
 * 或者 在 vi /etc/profile
 * 末尾加上两行
 * PATH=$PATH:/usr/sbin/php
 * export
 * 保存后 source /etc/profile
 * php -v
 * 完成
 */