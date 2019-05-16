<?php

namespace PhpureCore\Config;

use Closure;

class Arrow
{

    protected static $stack = array();

    public static function fetch(): array
    {
        return self::$stack;
    }

}