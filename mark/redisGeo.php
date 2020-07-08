<?php

//redis3.2中增中了对GEO类型的支持，该类型存储经纬度，提供了经纬设置，查询，范围查询，距离查询，经纬度hash等操作。
$redis = new Redis();
$redis->connect('127.0.0.1', 6379, 60);
$redis->auth('');

//添加成员的经纬度信息
$redis->rawCommand('geoadd', 'citys', '116.40', '39.90', 'beijing');
$redis->rawCommand('geoadd', 'citys', '121.47', '31.23', 'shanghai');
$redis->rawCommand('geoadd', 'citys', '114.30', '30.60', 'wuhan');

echo '<pre>';

//获取两个地理位置的距离，单位：m(米，默认)， km(千米)， mi(英里)， ft(英尺)
var_dump($redis->rawCommand('geodist', 'citys', 'beijing', 'wuhan'));
var_dump($redis->rawCommand('geodist', 'citys', 'beijing', 'shanghai', 'km'));

//获取成员的经纬度
var_dump($redis->rawCommand('geopos', 'citys', 'shanghai'));

//获取成员的经纬度hash，geohash表示坐标的一种方法，便于检索和存储
var_dump($redis->rawCommand('geohash', 'citys', 'shanghai', 'wuhan'));

//基于经纬度坐标的范围查询
//查询以经纬度为114，30为圆心，100千米范围内的成员
var_dump($redis->rawCommand('georadius', 'citys', '114', '30', '100', 'km'));

//WITHCOORD表示获取成员经纬度
var_dump($redis->rawCommand('georadius', 'citys', '114', '30', '100', 'km', 'WITHCOORD'));

//WITHDIST表示获取到圆心的距离
var_dump($redis->rawCommand('georadius', 'citys', '114', '30', '100', 'km', 'WITHDIST'));

//WITHHASH表示获取成员经纬度HASH值
var_dump($redis->rawCommand('georadius', 'citys', '114', '30', '100', 'km', 'WITHHASH'));

//COUNT 数量，表示限制获取成员的数量
var_dump($redis->rawCommand('georadius', 'citys', '114', '30', '100', 'km', 'COUNT', '3'));

// ASC 根据圆心位置，从近到远的返回元素
// DESC 根据圆心位置，从远到近的返回元素
var_dump($redis->rawCommand('georadius', 'citys', '114', '30', '100', 'km', 'ASC'));

//基于成员位置范围查询
//查询以武汉为圆心，100千米范围内的成员
var_dump($redis->rawCommand('georadiusbymember', 'citys', 'wuhan', '100', 'km'));