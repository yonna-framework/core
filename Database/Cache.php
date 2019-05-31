<?php

namespace PhpureCore\Database;

use PhpureCore\Glue\DB;

class Cache
{

    public static function get($key)
    {
        return DB::redis('cache')->get($key);
    }

    public static function set($key, $value, $timeout = 0)
    {
        DB::redis('cache')->set($key, $value, $timeout);
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