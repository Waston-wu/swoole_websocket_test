<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/14/014
 * Time: 20:56
 */
include './config/config.php';
$redis = new Redis();
$redis->connect($config->redis->host,$config->redis->port);

echo "Connection to server sucessfully";
//查看服务是否运行

echo "Server is running: " . $redis->ping();