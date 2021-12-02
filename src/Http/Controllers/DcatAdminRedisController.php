<?php

namespace Strays\DcatAdminRedis\Http\Controllers;

use Illuminate\Routing\Controller;
use Strays\DcatAdminRedis\RedisManager;

class DcatAdminRedisController extends Controller
{
    /**
     * @return mixed
     */
    protected function manager()
    {
        return RedisManager::instance();
    }
}
