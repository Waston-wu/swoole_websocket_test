<?php
$server = new swoole_websocket_server("0.0.0.0", 9502);
$server->on('open', function (swoole_websocket_server $server, $request) {

});
$server->on('message', function (swoole_websocket_server $server, $frame) {
    $data = json_decode($frame->data,true);
    if($data['connect']===TRUE)
    {
        $msg = '<span style="color:#ccc">'.date('Y-m-d H:i:s').' '.$data['msg'].'</span>';
    }elseif($data['connect']===FALSE)
    {
        $msg = ' <span style="color:red">' . $data['username'] . ' '. date('Y-m-d H:i:s') ."：</span><br>{$data['msg']}\n";
    }elseif($data['connect'] === 'barrage') // 弹幕
    {
        $msg = $data['msg'];
    }
    foreach ($server->connections as $v) {  // 所有在线用户！
        $server->push($v, $msg);
    }
});
$server->on('close', function ($server, $fd) {
    echo "client {$fd} closed\n";
});
$server->on('request', function (swoole_http_request $request, swoole_http_response $response) {
    global $server;//调用外部的server
    foreach ($server->connections as $fd) {
        $this->server->push($fd, $request->get['message']);
    }
});
$server->start();
