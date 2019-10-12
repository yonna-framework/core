<?php

namespace Yonna\Scope;

use Closure;
use Yonna\Core;
use Yonna\Throwable\Exception;
use Yonna\Middleware\After;
use Yonna\Middleware\Before;
use Yonna\Middleware\MiddlewareType;

/**
 * Class Config
 * @package Yonna\Scope
 */
class Config
{

    protected static $config = [];
    protected static $group = [];

    /**
     * @return array
     */
    public static function fetch(): array
    {
        return static::$config;
    }

    /**
     * 通用添加方法
     * @param string $method
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     * @throws null
     */
    private static function add(string $method, string $key, $call, string $action = null)
    {
        if (empty($method)) Exception::throw('no method');
        if (empty($key)) Exception::throw('no key');
        if (empty($call)) Exception::throw('no call class');
        // handle groups
        if (!empty(self::$group)) {
            $tempGroup = self::$group;
            $tempGroup[] = $key;
            $key = implode('_', $tempGroup);
        }
        // upper
        $method = strtoupper($method);
        $key = strtoupper($key);
        if (!isset(static::$config[$method])) {
            static::$config[$method] = [];
        }
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                !$action && Exception::throw("Should call a action for {$call}");
                $call = function ($request) use ($call, $action) {
                    $Scope = Core::get($call, $request);
                    if (!$Scope instanceof Scope) {
                        Exception::throw("Class {$call} is not instanceof Scope");
                    }
                    return $Scope->$action();
                };
            }
        }
        // if call instanceof Closure, combine the before and after
        if ($call instanceof Closure) {
            if (!isset(static::$config[$method][$key])) {
                static::$config[$method][$key] = ['before' => [], 'call' => null, 'after' => []];
            }
            // before
            $beforeFetch = Before::fetch();
            if ($beforeFetch) {
                foreach ($beforeFetch as $before) {
                    static::$config[$method][$key]['before'][] = $before;
                }
            }
            // call
            static::$config[$method][$key]['call'] = $call;
            // after
            $afterFetch = After::fetch();
            if ($afterFetch) {
                foreach ($afterFetch as $after) {
                    static::$config[$method][$key]['after'][] = $after;
                }
            }
        }
    }

    /**
     * POST
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public static function post(string $key, $call, string $action = 'index')
    {
        self::add('post', $key, $call, $action);
    }

    /**
     * GET
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public static function get(string $key, $call, string $action = 'index')
    {
        self::add('get', $key, $call, $action);
    }

    /**
     * PUT
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public static function put(string $key, $call, string $action = 'index')
    {
        self::add('put', $key, $call, $action);
    }

    /**
     * DELETE
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public static function delete(string $key, $call, string $action = 'index')
    {
        self::add('delete', $key, $call, $action);
    }

    /**
     * PATCH
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public static function patch(string $key, $call, string $action = 'index')
    {
        self::add('patch', $key, $call, $action);
    }

    /**
     * STREAM
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public static function stream(string $key, $call, string $action = 'index')
    {
        self::add('stream', $key, $call, $action);
    }

    /**
     * group
     * @param $key
     * @param Closure $closure
     * @throws null
     */
    public static function group($key, Closure $closure)
    {
        if (is_string($key)) {
            self::$group[] = $key;
        } else if (is_array($key)) {
            self::$group = array_merge(self::$group, $key);
        }
        $closure();
        self::$group = [];
    }

    /**
     * middleware
     * @param $call
     * @param Closure $closure
     * @throws null
     */
    public static function middleware($call, Closure $closure)
    {
        if (empty($call)) {
            Exception::throw('middleware should has some called');
        }
        if (is_string($call)) {
            $call = [$call];
        }
        if (!is_array($call)) {
            Exception::throw('middleware not allow this called');
        }
        foreach ($call as $c) {
            $t = $c::type();
            switch ($t) {
                case MiddlewareType::BEFORE:
                    Before::add($c);
                    break;
                case MiddlewareType::AFTER:
                    After::add($c);
                    break;
                default:
                    Exception::throw('middleware not support ' . $t);
                    break;
            }
        }
        $closure();
        Before::clear();
        After::clear();
    }

}