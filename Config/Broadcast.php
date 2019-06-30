<?php

namespace Yonna\Config;

use Closure;
use Yonna\Exception\Exception;
use Yonna\Glue\Response;

class Broadcast extends Arrow
{

    const name = 'broadcast';

    /**
     * @param string $scope
     * @param Closure $call
     */
    public static function scope(string $scope, Closure $call)
    {
        if (empty($scope)) Exception::throw('no scope');
        if (empty($call)) Exception::throw('no call');
        if (!isset(self::$stack[self::name][$scope])) {
            self::$stack[self::name][$scope] = array();
        }
        array_push(self::$stack[self::name][$scope], $call);
    }

}