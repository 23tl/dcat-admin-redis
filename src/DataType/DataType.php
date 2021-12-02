<?php

namespace Strays\DcatAdminRedis\DataType;

abstract class DataType
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    abstract public function fetch(string $key);

    abstract public function update(array $params);

    abstract public function store(array $params);
}
