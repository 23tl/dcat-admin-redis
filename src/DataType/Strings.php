<?php

namespace Strays\DcatAdminRedis\DataType;

use Strays\DcatAdminRedis\Support\Arr;
use Strays\DcatAdminRedis\Support\Str;

class Strings extends DataType
{

    public function fetch(string $key)
    {
        return $this->getConnection()->get($key);
    }


    public function update(array $params)
    {
        $this->store($params);
    }


    /**
     * @param array $params
     */
    public function store(array $params)
    {
        $prefix = config('database.redis.options.prefix');
        $key = Str::cutStr(Arr::get($params, 'key', null), $prefix);
        $origin = Str::cutStr(Arr::get($params, 'origin'), $prefix);
        $value = Arr::get($params, 'value');
        $ttl = Arr::get($params, 'ttl');

        $this->getConnection()->set($origin, $value);

        if ($ttl > 0) {
            $this->getConnection()->expire($origin, $ttl);
        }

        if ($key !== $origin) {
            $this->getConnection()->rename($origin, $key);
        }
    }
}
