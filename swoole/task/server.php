<?php
/**
 * Swoole Task
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
    'task_worker_num' => 4,
]);

// 监听连接进入事件
$server->on('Connect', function ($server, $fd) {
    echo "Connect.\n";
});

// 监听数据接收事件
$server->on('Receive', function (\Swoole\Server $server, $fd, $reactorId, $data) {

    $welcome = new WelcomeController();
    $ret = $welcome->hello(substr($data, 4));

    // 投递异步任务
    $taskId = $server->task($ret);
    $ret .= "[TaskId: {$taskId}] ";
    $len = pack('N', strlen($ret));

    $server->send($fd, $len . $ret);
});

// 处理异步任务
$server->on('Task', function (\Swoole\Server $server, $taskId, $reactorId, $data) {
    echo "New AsyncTask[id={$taskId}]\n";

    sleep(mt_rand(10, 20));

    $server->finish("{$data} -- OK");
});

// 处理异步任务结束
$server->on('Finish', function (\Swoole\Server $server, $taskId, $data) {
    echo "AsyncTask[id={$taskId}] Finish: {$data}\n";
});

// 监听连接关闭事件
$server->on('Close', function (\Swoole\Server $server, $fd) {
    echo "Client: Close.\n";
});

$server->start();