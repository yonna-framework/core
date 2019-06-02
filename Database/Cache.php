<?php

namespace PhpureCore\Database;

use PhpureCore\Config\Arrow;
use PhpureCore\Glue\DB;

class Cache
{

    const DEFAULT_MINIMUM_TIMEOUT = 10;

    /**
     * get timeout
     * 获取合理的过期时间
     * @param $timeout
     * @return array|bool|false|int|string|null
     */
    private static function timeout(int $timeout): int
    {
        if ($timeout <= 0) return 0;
        $min_timeout = Arrow::env('DB_CACHE_MINIMUM_TIMEOUT') ?? self::DEFAULT_MINIMUM_TIMEOUT;
        if ($timeout < $min_timeout) $timeout = $min_timeout;
        return $timeout;
    }

    public static function get($key)
    {
        return DB::redis('cache')->get($key);
    }

    public static function set($key, $value, int $timeout = 0)
    {
        DB::redis('cache')->set($key, $value, self::timeout($timeout));
    }

    public static function uGet($uniqueCode, $key)
    {
        return DB::redis('cache')->hGet($uniqueCode, $key);
    }

    public static function uSet($uniqueCode, $key, $value)
    {
        DB::redis('cache')->hSet($uniqueCode, $key, $value);
    }

    public static function clear($uniqueCode)
    {
        DB::redis('cache')->delete($uniqueCode);
    }

}