<?php

namespace PhpureCore\Scope;

use Closure;
use PhpureCore\Core;
use PhpureCore\Glue\Handle;

class Neck
{

    private static $neck = [];

    /**
     * 添加 neck
     * @param Closure | string $call
     */
    public static function add($call)
    {
        if (empty($call)) Handle::exception('no call');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                $call = function ($request) use ($call) {
                    Core::get($call, $request)->handle();
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
        var_dump(static::$neck);
        $n = static::$neck;
        static::$neck = [];
        return $n;
    }

}