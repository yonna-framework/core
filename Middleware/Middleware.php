<?php

namespace Yonna\Middleware;

/**
 * class Middleware
 * @package Core\Core\scope
 */
abstract class Middleware implements Interfaces\Middleware
{

    /**
     * get middleware
     * @return string
     */
    public static function type(): string
    {
        return MiddlewareType::MIDDLEWARE;
    }

    public function handle($params)
    {
    }

}