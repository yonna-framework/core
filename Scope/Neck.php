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
        if (empty($call)) Exception::throw('no call class');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                $call = function ($request, ...$params) use ($call) {
                    $Neck = Core::get($call, $request);
                    if (!$Neck instanceof Middleware) {
                        Exception::throw("Class {$call} is not instanceof Middleware");
                    }
                    $Neck->handle($params);
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