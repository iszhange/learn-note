<?php
/**
 * Demo示例
 */

namespace App\Controller;

use Swoole\Http\Request;
use Swoole\Http\Response;

class WelcomeController
{

    public function index(Request $request, Response $response)
    {
        $response->end('request id:' . mt_rand(10000, 999999) . ' ' . ($request->get['name'] ?? 'Unknown'));
    }

}