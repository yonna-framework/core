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
     * @param Closure | string $callClass
     */
    public static function add($callClass)
    {
        if (empty($callClass)) Exception::throw('no call class');
        // if call instanceof string, convert it to Closure
        if (is_string($callClass)) {
            if (class_exists($callClass)) {
                $call = function ($request, ...$params) use ($callClass) {
                    $Neck = Core::get($callClass, $request);
                    if (!$Neck instanceof Middleware) {
                        Exception::throw("Class {$callClass} is not instanceof Middleware");
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