<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2018/12/17
 * Time: 16:43
 */
namespace Imooc;

class Singleton
{
    /**
     * 单例模式使某个类的对象仅能创建一次，通常一个项目中会多次用的Db这个数据库连接类，
     * 如果在每个地方都调用工厂方法创建一个数据库连接类，这样是比较消耗资源的，
     * 我们只需要一个数据库连接，单例模式就是来解决这个问题的。
    我们打开Db类,首先把构造方法设置为私有的,这样就禁止了在其他地方直接new我们的Db类
    1.什么是单例模式：一个类最多只能产生一个对象，如果希望在系统中某个类（链接数据库的类）的对象只能存在一个，单例模式是最好的解决方案。
    2.单利模式的实现:三私一公
    ①私有化构造方法:防止实例化
    ②私有化克隆方法:防止克隆
    ③私有化静态属性:保存对象
    ④公有化静态方法:获取对象
     */
    private static $obj;//私有化静态属性
    private function __construct()
    {
        //私有化构造方法
    }
    private function __clone()
    {
        //私有化克隆方法
    }
    //静态方法产生对象
    static public function getInstance()
    {
        //对象不存在new 一个对象
        if(!is_object(self::$obj)){
            self::$obj = new Singleton();
        }
        return self::$obj;
    }
}