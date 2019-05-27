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

}