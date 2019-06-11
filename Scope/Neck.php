<?php

namespace PhpureCore\Scope;

use Closure;
use PhpureCore\Core;
use PhpureCore\Exception\Exception;

class Neck
{

    private static $neck = [];

    /**
     * 添加 neck
     * @param Closure | string $call
     */
    public static function add($call)
    {
        if (empty($call)) Exception::throw('no call');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                $call = function ($request, ...$params) use ($call) {
                    Core::get($call, $request)->handle($params);
                };
            }
        } // if call instanceof Closure, combine the middleware and
        if ($call instanceof Closure) {
            static::$neck[] = $call;
        }
    }

    /**
     * 获取 neck
     * @return array
     */
    public static function fetch()
    {
        $n = static::$neck;
        static::$neck = [];
        return $n;
    }

}