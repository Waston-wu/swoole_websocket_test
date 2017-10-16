<?php
include './config/config.php';
$redis = new Redis();
$redis->connect($config->redis->host,$config->redis->port); // 连接redis
if(!$redis->exists('msg'))$redis->set('msg','');    // 检测消息内容是否存在
$server = new swoole_websocket_server("0.0.0.0", 9502); // 连接swoole
$server->on('open', function (swoole_websocket_server $server, $request) {
    global $redis;
    $redis->set($request->fd,$request->get['username']);
    // 为新加入的用户展示历史消息
    global $server;
    $server->push($request->fd,$redis->get('msg'));
});
$server->on('message', function (swoole_websocket_server $server, $frame) {
    $data = json_decode($frame->data,true);
    if($data['connect']===TRUE)
    {
        $msg = '<span style="color:#ccc">'.date('Y-m-d H:i:s').' '.$data['msg']."</span>\n"; // 进入房间提示
    }elseif($data['connect']===FALSE)
    {
        $msg = ' <span style="color:red">' . $data['username'] . ' '. date('Y-m-d H:i:s') ."：</span><br>{$data['msg']}\n <br>";  // 消息
    }elseif($data['connect'] === 'barrage') // 弹幕
    {
        $msg = $data['msg'];

    }
    global $redis;
    $old_msg = $redis->get('msg'); // 获取历史消息
    $new_msg = $old_msg.$msg; // 组合新的消息内容
    $redis->set('msg',$new_msg); // 将新的redis存入redis
    foreach ($server->connections as $v) {  // 所有在线用户！
        $server->push($v, $msg);
    }
});
$server->on('close', function ($server, $fd) {
    echo "client {$fd} closed\n";
    global $redis;
    $msg = $redis->get($fd).'离开了房间<br>';
    foreach ($server->connections as $v) {  // 所有在线用户！
        if($v == $fd)continue;  // 关闭的用户不发送，不然报错
        $server->push($v, $redis->get($fd).'离开了房间<br>');
    }
    $old_msg = $redis->get('msg'); // 获取历史消息
    $new_msg = $old_msg.$msg; // 组合新的消息内容

    $redis->set('msg',$new_msg); // 将新的redis存入redis
    $redis->del($fd);
});
$server->on('request', function (swoole_http_request $request, swoole_http_response $response) {
    global $server;//调用外部的server
    foreach ($server->connections as $fd) {
        $this->server->push($fd, $request->get['message']);
    }
});
$server->start();
