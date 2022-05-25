<?php
/**
 * Swoole TCP
 */
require 'vendor/autoload.php';

use App\Controller\WelcomeController;

$server = new Swoole\Server('0.0.0.0', 9501);

$server->set([
    'open_length_check' => true,
    'package_max_length' => 81920,
    'package_length_type' => 'N', //see php pack()
    'package_length_offset' => 0,
    'package_body_offset' => 4,
]);

// 监听连接进入事件
$server->on('Connect', function ($server, $fd) {
    echo "Connect.\n";
});

// 监听数据接收事件
$server->on('Receive', function (\Swoole\Server $server, $fd, $reactorId, $data) {

    $welcome = new WelcomeController();
    $ret = $welcome->hello(substr($data, 4));
    $len = pack('N', strlen($ret));

    $server->send($fd, $len . $ret);
});

// 监听连接关闭事件
$server->on('Close', function (\Swoole\Server $server, $fd) {
    echo "Client: Close.\n";
});

$server->start();