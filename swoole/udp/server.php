<?php
/**
 * Swoole UDP
 */
require 'vendor/autoload.php';

use App\Controller\WelcomeController;

$server = new Swoole\Server('0.0.0.0', 9501, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

// 监听数据接收事件
$server->on('Packet', function (\Swoole\Server $server, $data, $clientInfo) {

    $welcome = new WelcomeController();
    $ret = $welcome->hello($data);

    $server->sendto($clientInfo['address'], $clientInfo['port'], "Server: {$ret}");
});

$server->start();