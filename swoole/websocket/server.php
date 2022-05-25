<?php
/**
 * Swoole UDP
 */
require 'vendor/autoload.php';

use App\Controller\WelcomeController;

$server = new Swoole\WebSocket\Server('0.0.0.0', 9501);

// 监听连接打开事件
$server->on('Open', function (\Swoole\WebSocket\Server $server, $request) {
    echo "Server: handshake success with fd {$request->id}\n";
});

// 监听消息事件
$server->on('Message', function (\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame) {
    echo "Message: {$frame->data}\n";
    $server->push($frame->fd, "Server: {$frame->data}");
});

// 监听关闭事件
$server->on('Close', function (\Swoole\WebSocket\Server $server, $fd) {
    echo "Client: {$fd} closed!\n";
});

$server->on('Request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($server) {
    if ($request->server['request_uri'] == '/message') {
        foreach ($server->connections as $fd)
        {
            if ($server->isEstablished($fd)) {
                $server->push($fd, $request->get['message']);
            }
        }
        $response->end('ok');
    }
});

$server->start();