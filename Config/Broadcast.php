<?php

namespace PhpureCore\Config;

use Closure;
use Exception;
use PhpureCore\Cargo;

class Broadcast extends Arrow
{

    const name = 'broadcast';

    public function __construct()
    {
        if (!isset(self::$stack[self::name])) {
            self::$stack[self::name] = array();
        }
        return $this;
    }

    /**
     * @param string $scope
     * @param Closure $call
     */
    public static function scope(string $scope, Closure $call)
    {
        if (empty($scope) || empty($call)) return;
        if (!isset(self::$stack[self::name][$scope])) {
            self::$stack[self::name][$scope] = array();
        }
        array_push(self::$stack[self::name][$scope], $call);
    }

}