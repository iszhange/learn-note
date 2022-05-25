<?php
/**
 * Swoole MQTT
 */
require 'vendor/autoload.php';

use App\Controller\WelcomeController;

$server = new Swoole\Server('0.0.0.0', 9501, SWOOLE_BASE);

$server->set([
    'open_mqtt_protocol' => true, // 启用MQTT协议
    'worker_num' => 1,
]);

// 监听连接进入事件
$server->on('Connect', function (\Swoole\Server $server, $fd) {
    echo "Connect.\n";
});

// 监听数据接收事件
$server->on('Receive', function (\Swoole\Server $server, $fd, $reactorId, $data) {
    $header = \App\Library\MQTT::getHeader($data);
    var_dump($header);

    if ($header['type'] == 1) {
        $resp = chr(32) . chr(2) . chr(0) . chr(0);
        \App\Library\MQTT::connect($header, substr($data, 2));
        $server->send($fd, $resp);
    } elseif ($header['type'] == 3) {
        $offset = 2;
        $topic = \App\Library\MQTT::decodeString(substr($data, $offset));
        $offset += strlen($topic) + 2;
        $msg = substr($data, $offset);
        echo "Client msg: {$topic} -- {$msg}\n";
    }
    echo "received length=" . strlen($data) . "\n";
});

// 监听连接关闭事件
$server->on('Close', function (\Swoole\Server $server, $fd) {
    echo "Client: Close.\n";
});

$server->start();