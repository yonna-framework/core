<?php

namespace PhpureCore\Scope;

use Closure;
use PhpureCore\Core;
use PhpureCore\Exception\Exception;
use PhpureCore\Mapping\MiddleType;

class Before extends Middleware
{

    private static $before = [];


    /**
     * get middleware
     * @return string
     */
    public static function type(): string
    {
        return MiddleType::BEFORE;
    }

    /**
     * 添加 before
     * @param Closure | string $call
     */
    public static function add($call)
    {
        if (empty($call)) Exception::throw('no call class');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                $call = function ($request, ...$params) use ($call) {
                    $Before = Core::get($call, $request);
                    if (!$Before instanceof Before) {
                        Exception::throw("Class {$call} is not instanceof Middleware-Before");
                    }
                    $Before->handle($params);
                };
            }
        } // if call instanceof Closure, combine the middleware and
        if ($call instanceof Closure) {
            static::$before[] = $call;
        }
    }

    /**
     * 获取 before
     * @return array
     */
    public static function fetch()
    {
        return static::$before;
    }

    /**
     * 清空before
     */
    public static function clear(){
        static::$before = [];
    }

}