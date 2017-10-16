<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/14/014
 * Time: 20:57
 */
$config = new Stdclass();

// redis 配置
$config->redis = new Stdclass();
$config->redis->host = '127.0.0.1';
$config->redis->port = 6379;