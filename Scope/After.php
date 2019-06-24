<?php

namespace PhpureCore\Scope;

use Closure;
use PhpureCore\Core;
use PhpureCore\Exception\Exception;

class After
{

    private static $after = [];

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
                    if (!$After instanceof Middleware) {
                        Exception::throw("Class {$call} is not instanceof Middleware");
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
        $n = static::$after;
        static::$after = [];
        return $n;
    }

}