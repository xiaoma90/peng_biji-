<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2018/12/17
 * Time: 16:16
 */
/**
 * 1.工厂模式
工厂模式是用工厂方法生成对象，而不是直接new一个对象。
 */

namespace Imooc;

class Factory
{
    static public function createDb()
    {
        $db = new Db();
        return $db;
    }
}

$db = Factory::createDb();