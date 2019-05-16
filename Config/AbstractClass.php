<?php

namespace PhpureCore\Config;

use Closure;

class AbstractClass
{

    protected static $stack = array();

    public static function fetch(): array
    {
        return self::$stack;
    }

}