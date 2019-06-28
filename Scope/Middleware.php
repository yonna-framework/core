<?php

namespace PhpureCore\Scope;

use PhpureCore\Mapping\MiddleType;

/**
 * class Middleware
 * @package phpurecore\scope
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