<?php

namespace Strays\DcatAdminRedis\Support;

use Strays\DcatAdminRedis\DcatAdminRedisServiceProvider;

class Trans
{
    /**
     * 快速翻译（为了缩短代码量后期易扩展）.
     */
    public static function get(string $key): string
    {
        return DcatAdminRedisServiceProvider::trans($key);
    }
}
