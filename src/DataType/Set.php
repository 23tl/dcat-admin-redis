<?php


namespace Strays\DcatAdminRedis\DataType;


class Set extends DataType
{
    public function fetch(string $key)
    {
        return $this->getConnection()->sMembers($key);
    }


    public function update(array $params)
    {
        return $this->getConnection()->transaction(
            function ($q) use ($params) {
                $q->srem($params['hash'], $params['key']);
                $q->sadd($params['hash'], $params['key']);
            }
        );
    }


    public function store(array $params)
    {
    }


    public function remove(array $params)
    {
        return $this->getConnection()->srem($params['hash'], $params['key']);
    }
}
