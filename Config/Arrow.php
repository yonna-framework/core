<?php

namespace PhpureCore\Config;

class Arrow
{

    protected static $stack = array();

    public static function fetch(): array
    {
        return static::$stack;
    }

}