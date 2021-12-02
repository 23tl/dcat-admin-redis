<?php

namespace Strays\DcatAdminRedis\Support;

class Arr
{
    /**
     * 获取数组中某值
     *
     * @param $key
     * @param null $default
     *
     * @return mixed|null
     */
    public static function get(array $array, $key, $default = null)
    {
        return $array[$key] ?? $default;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public static function exists(array $array, $key)
    {
        return array_key_exists($key, $array);
    }
}
