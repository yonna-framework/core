<?php

namespace PhpureCore\Scope;

use Closure;
use PhpureCore\Core;
use PhpureCore\Glue\Response;

class Tail
{

    private static $tail = [];

    /**
     * 添加 tail
     * @param Closure | string $call
     */
    public static function add($call)
    {
        if (empty($call)) Response::exception('no call');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                $call = function ($request, ...$params) use ($call) {
                    Core::get($call, $request)->handle($params);
                };
            }
        } // if call instanceof Closure, combine the middleware and
        if ($call instanceof Closure) {
            static::$tail[] = $call;
        }
    }

    /**
     * 获取 tail
     * @return array
     */
    public static function fetch()
    {
        $n = static::$tail;
        static::$tail = [];
        return $n;
    }

}