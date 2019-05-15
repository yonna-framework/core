<?php

namespace PhpureCore\Config;

use Closure;

class Broadcast
{

    private static $stack = array();

    public static function scope(string $scope, Closure $call)
    {
        if (!isset(self::$stack[$scope])) {
            self::$stack[$scope] = array();
        }
        array_push(self::$stack[$scope], $call);
    }

    public static function fetch(): array
    {
        return self::$stack;
    }

}