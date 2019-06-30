<?php

namespace Yonna\Scope;

use Closure;
use Yonna\Core;
use Yonna\Exception\Exception;
use Yonna\Mapping\MiddleType;

class After extends Middleware
{

    private static $after = [];


    /**
     * get middleware
     * @return string
     */
    public static function type(): string
    {
        return MiddleType::AFTER;
    }

    /**
     * 添加 after
     * @param Closure | string $call
     */
    public static function add($call)
    {
        if (empty($call)) Exception::throw('no call class');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                $call = function ($request, ...$params) use ($call) {
                    $After = Core::get($call, $request);
                    if (!$After instanceof After) {
                        Exception::throw("Class {$call} is not instanceof Middleware-After");
                    }
                    $After->handle($params);
                };
            }
        } // if call instanceof Closure, combine the middleware and
        if ($call instanceof Closure) {
            static::$after[] = $call;
        }
    }

    /**
     * 获取 after
     * @return array
     */
    public static function fetch()
    {
        return static::$after;
    }

    /**
     * 清空before
     */
    public static function clear()
    {
        static::$after = [];
    }

}