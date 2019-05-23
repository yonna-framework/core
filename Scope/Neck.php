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
     * @param string $action
     */
    public static function add($call, string $action = null)
    {
        if (empty($call)) Handle::exception('no call');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                !$action && Handle::exception("Should call a action for {$call}");
                $call = function ($request) use ($call, $action) {
                    Core::get($call, $request)->$action();
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
    public static function fetch(){
        $n =  static::$neck;
        static::$neck = [];
        return $n;
    }

}