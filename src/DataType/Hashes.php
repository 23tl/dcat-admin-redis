<?php

namespace Strays\DcatAdminRedis\DataType;

class Hashes extends DataType
{

    public function fetch(string $key)
    {
        return $this->getConnection()->hgetall($key);
    }


    public function update(array $params)
    {
        return $this->getConnection()->hset(
            $params['hash'],
            $params['key'],
            $params['value'],
        );
    }


    public function store(array $params)
    {
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function remove(array $params)
    {
        return $this->getConnection()->hdel($params['hash'], $params['key']);
    }
}
