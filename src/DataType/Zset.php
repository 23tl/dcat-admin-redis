<?php

namespace Strays\DcatAdminRedis\DataType;

class Zset extends DataType
{
    public function fetch(string $key)
    {
        return $this->getConnection()->zrange($key, 0, -1, true);
    }

    public function update(array $params)
    {
        if ($params['oScore'] !== $params['score']) {
            $this->getConnection()->zadd($params['hash'], $params['score'], $params['oMember']);
        }

        if ($params['oMember'] !== $params['member']) {
            $this->getConnection()->transaction(
                function ($q) use ($params) {
                    $q->zrem($params['hash'], $params['oMember']);
                    $q->zadd($params['hash'], $params['score'], $params['member']);
                }
            );
        }
    }

    public function store(array $params)
    {
    }

    public function remove(array $params)
    {
        return $this->getConnection()->zrem($params['hash'], $params['key']);
    }
}
