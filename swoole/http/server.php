<?php
/**
 * Swoole HTTP
 */
require 'vendor/autoload.php';

$routers = [
    '/welcome' => 'App\Controller\WelcomeController@index'
];

$http = new Swoole\Http\Server('0.0.0.0', 9501);
$http->on('Request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use($routers) {
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        if (is_file('favicon.ico')) {
            $response->header('Content-Type', 'image/jpeg');
            $response->sendFile('favicon.ico');
        } else {
            $response->end();
        }
        return;
    }

    $response->header('Content-Type', 'text/json');
    $router = $routers[$request->server['request_uri']] ?? '';
    if ($router) {
        list($controller, $method) = explode('@', $router);
        call_user_func_array([new $controller, $method], [$request, $response]);
    } else {
        $response->end('Not Found!');
    }

});
$http->start();