<?php

// 一键协程化
Co::set(['hook_flags' => SWOOLE_HOOK_ALL | SWOOLE_HOOK_CURL]);

\Swoole\Coroutine\run(function () {
    $data = '小明';
    $len = pack('N', strlen($data));
    for ($i=0; $i<100; $i++)
    {
        go(function () use ($data, $len) {
            $client = new \Swoole\Client(SWOOLE_SOCK_UDP);

            if (!$client->connect('0.0.0.0', 9501, -1)) {
                exit("Connect failed. Error: {$client->errCode}\n");
            }

            $client->send($data);
            echo $client->recv();

            $client->close();
        });

    }

});


