<?php

namespace PhpureCore\Config;

use Closure;

class Broadcast extends AbstractClass
{

    public static function scope(string $scope, Closure $call)
    {
        if (!isset(self::$stack[$scope])) {
            self::$stack[$scope] = array();
        }
        array_push(self::$stack[$scope], $call);
    }

}