<?php

namespace Strays\DcatAdminRedis;

use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
use Redis as BaseRedis;
use Strays\DcatAdminRedis\DataType\Hashes;
use Strays\DcatAdminRedis\DataType\Lists;
use Strays\DcatAdminRedis\DataType\Set;
use Strays\DcatAdminRedis\DataType\Strings;
use Strays\DcatAdminRedis\DataType\Zset;
use Strays\DcatAdminRedis\Support\Arr;
use Strays\DcatAdminRedis\Support\Str;

class RedisManager
{
    public static $typeColor = [
        'string' => 'primary',
        'list' => 'info',
        'zset' => 'danger',
        'hash' => 'warning',
        'set' => 'success',
    ];

    /**
     * @var
     */
    protected static $instance;

    /**
     * Redis 实例.
     *
     * @var
     */
    protected $connection;

    /**
     * RedisManager constructor.
     *
     * @param string $connection
     */
    public function __construct($connection = 'default')
    {
        $this->connection = $connection;
    }

    /**
     * @param string|null $connection
     *
     * @return mixed
     */
    public static function instance($connection = 'default')
    {
        if (!self::$instance instanceof self) {
            static::$instance = new static($connection);
        }

        return static::$instance;
    }

    /**
     * 获取当前 Redis 实例.
     *
     * @param null $connection
     */
    public function getConnection($connection = null): Connection
    {
        if ($connection) {
            $this->connection = $connection;
        }

        return Redis::connection($this->connection);
    }

    /**
     * 获取当前 Redis 信息.
     *
     * @return mixed
     */
    public function getInformation()
    {
        return $this->getConnection()->info();
    }

    /**
     * @param int    $cursor
     * @param string $pattern
     * @param int    $count
     *
     * @return array
     */
    public function scan($cursor = 0, $pattern = '*', $count = 10)
    {
        $client = $this->getConnection();
        $keys = [];
        $result = $client->scan(
            $cursor,
            [
                'match' => $pattern.'*',
                'count' => $count,
            ]
        );

        if (!is_array($result)) {
            return [
                'cursor' => 0,
                'data' => [],
            ];
        }

        // 此处判断如果未搜索到，则进行递归搜索
        if (2 === count($result) && $result[0] > 0 && count($result[1]) <= $count) {
            return $this->scan($result[0], $pattern, $count);
        }

        $script = <<<'LUA'
        local type = redis.call('type', KEYS[1])["ok"]
        local ttl = redis.call('ttl', KEYS[1])

        return {KEYS[1], type, ttl}
LUA;

        $data = $client->pipeline(
            function ($pipe) use ($result, $script) {
                foreach ($result[1] as $key) {
                    $pipe->eval($script, [$this->getRedisKey($key)], 1);
                }
            }
        );

        foreach ($data as $key) {
            $keys[] = [
                'key' => $key[0],
                'type' => $key[1],
                'ttl' => $key[2],
            ];
        }

        return [
            'cursor' => $result[0],
            'data' => $keys,
        ];
    }

    /**
     * @return mixed
     */
    public function fetch(string $key)
    {
        $key = $this->getRedisKey($key);
        $this->exists($key);
        $type = $this->getConnection()->type($key);

        return $this->getDataType($type)->fetch($key);
    }

    public function update(array $params)
    {
        $key = $this->getRedisKey(Arr::get($params, 'origin'));
        $this->exists($key);
        $type = $this->getConnection()->type($key);

        return $this->getDataType($type)->update($params);
    }

    /**
     * @return mixed
     */
    public function ttl(string $key)
    {
        $key = $this->getRedisKey($key);
        $this->exists($key);

        return $this->getConnection()->ttl($key);
    }

    /**
     * 删除缓存.
     */
    public function delKeys(array $keys)
    {
        $client = $this->getConnection();
        $script = <<<'LUA'
        return redis.call('del', KEYS[1])
LUA;
        $client->pipeline(
            function ($pipe) use ($keys, $script) {
                foreach ($keys as $key) {
                    $pipe->eval($script, [$this->getRedisKey($key)], 1);
                }
            }
        );
    }

    /**
     * @return string
     */
    public function getRedisKey(string $key)
    {
        $prefix = config('database.redis.options.prefix');

        return Str::cutStr($key, $prefix);
    }

    /**
     * @return Strings|Hashes|Set|Zset|Lists|array
     */
    public function getDataType(int $type)
    {
        switch ($type) {
            case BaseRedis::REDIS_STRING:
                return new Strings($this->getConnection());
            case BaseRedis::REDIS_HASH:
                return new Hashes($this->getConnection());
            case BaseRedis::REDIS_SET:
                return new Set($this->getConnection());
            case BaseRedis::REDIS_ZSET:
                return new Zset($this->getConnection());
            case BaseRedis::REDIS_LIST:
                return new Lists($this->getConnection());
            case BaseRedis::REDIS_NOT_FOUND:
            default:
                return [];
        }
    }

    /**
     * @return array
     */
    protected function exists(string $key)
    {
        if (!$this->getConnection()->exists($key)) {
            return [];
        }
    }
}
