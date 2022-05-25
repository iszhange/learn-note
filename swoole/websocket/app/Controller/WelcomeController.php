<?php
/**
 * Demo示例
 */

namespace App\Controller;

use Swoole\Http\Request;
use Swoole\Http\Response;

class WelcomeController
{

    public function hello($name)
    {
        return 'Hello World! ' . $name;
    }

}