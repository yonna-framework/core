<?php

namespace PhpureCore\Scope;

use Closure;
use PhpureCore\Core;
use PhpureCore\Exception\Exception;

class Before
{

    private static $before = [];

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
                    if (!$Before instanceof Middleware) {
                        Exception::throw("Class {$call} is not instanceof Middleware");
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
        $n = static::$before;
        static::$before = [];
        return $n;
    }

}