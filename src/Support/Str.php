<?php

namespace Strays\DcatAdminRedis\Support;

class Str
{
    /**
     * 返回截取指定字符串后面的字符.
     *
     * @param string      $string 字符串
     * @param string|null $sign   指定字符串
     *
     * @return string
     */
    public static function cutStr(string $string, string $sign = null)
    {
        if (!$sign) {
            return $string;
        }
        if (false === strpos($string, $sign)) {
            return $string;
        }

        return ltrim(strrchr($string, $sign), $sign);
    }
}
