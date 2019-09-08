<?php

namespace Yonna\Middleware;

/**
 * class Middleware
 * @package Core\Core\scope
 */
abstract class Middleware implements Interfaces\Middleware
{

    protected static $type = MiddlewareType::MIDDLEWARE;

    /**
     * get middleware
     * @return string
     */
    public static function type(): string
    {
        return static::$type;
    }

}