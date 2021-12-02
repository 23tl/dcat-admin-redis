<?php

namespace Strays\DcatAdminRedis\DataType;

class Lists extends DataType
{
    public function fetch(string $key)
    {
        return $this->getConnection()->lrange($key, 0, -1);
    }

    public function update(array $params)
    {
    }

    public function store(array $params)
    {
    }

    public function remove(array $params)
    {
//        return $this->getConnection()->transaction(function ($q) use ($params) {
//            $q->lset($params['hash'], 0, $params['key']);
//            $q->lrem($params['hash'], $params['key'], 2);
//        });
    }
}
