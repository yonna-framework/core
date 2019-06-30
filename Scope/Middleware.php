<?php

namespace Yonna\Scope;

use Yonna\Mapping\MiddleType;

/**
 * class Middleware
 * @package Core\Core\scope
 */
abstract class Middleware extends Kernel implements Interfaces\Middleware
{

    /**
     * get middleware
     * @return string
     */
    public static function type(): string
    {
        return MiddleType::MIDDLEWARE;
    }

    public function handle($params)
    {
    }

}