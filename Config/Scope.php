<?php

namespace Yonna\Config;

use Closure;
use Yonna\Core;
use Yonna\Exception\Exception;
use Yonna\Mapping\MiddleType;
use Yonna\Scope\After;
use Yonna\Scope\Before;

class Scope extends Arrow
{

    const name = 'scope';

    public function __construct()
    {
        return $this;
    }

    /**
     * 通用添加方法
     * @param string $method
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    private function add(string $method, string $key, $call, string $action = null)
    {
        if (empty($method)) Exception::throw('no method');
        if (empty($key)) Exception::throw('no key');
        if (empty($call)) Exception::throw('no call class');
        // upper
        $method = strtoupper($method);
        $key = strtoupper($key);
        if (!isset(static::$stack[self::name][$method])) {
            static::$stack[self::name][$method] = [];
        }
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                !$action && Exception::throw("Should call a action for {$call}");
                $call = function ($request) use ($call, $action) {
                    $Scope = Core::get($call, $request);
                    if (!$Scope instanceof \Yonna\Scope\Scope) {
                        Exception::throw("Class {$call} is not instanceof Scope");
                    }
                    return $Scope->$action();
                };
            }
        }
        // if call instanceof Closure, combine the before and after
        if ($call instanceof Closure) {
            if (!isset(static::$stack[self::name][$method][$key])) {
                static::$stack[self::name][$method][$key] = ['before' => [], 'call' => null, 'after' => []];
            }
            // before
            $beforeFetch = Before::fetch();
            if ($beforeFetch) {
                foreach ($beforeFetch as $before) {
                    static::$stack[self::name][$method][$key]['before'][] = $before;
                }
            }
            // call
            static::$stack[self::name][$method][$key]['call'] = $call;
            // after
            $afterFetch = After::fetch();
            if ($afterFetch) {
                foreach ($afterFetch as $after) {
                    static::$stack[self::name][$method][$key]['after'][] = $after;
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
    public function post(string $key, $call, string $action = 'post')
    {
        $this->add('post', $key, $call, $action);
    }

    /**
     * GET
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public function get(string $key, $call, string $action = 'get')
    {
        $this->add('get', $key, $call, $action);
    }

    /**
     * PUT
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public function put(string $key, $call, string $action = 'put')
    {
        $this->add('put', $key, $call, $action);
    }

    /**
     * DELETE
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public function delete(string $key, $call, string $action = 'delete')
    {
        $this->add('delete', $key, $call, $action);
    }

    /**
     * PATCH
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     */
    public function patch(string $key, $call, string $action = 'patch')
    {
        $this->add('patch', $key, $call, $action);
    }

    /**
     * middleware
     * @param $call
     * @param Closure $closure
     * @return $this
     */
    public function middleware($call, Closure $closure)
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
                case MiddleType::BEFORE:
                    Before::add($c);
                    break;
                case MiddleType::AFTER:
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
        return $this;
    }

}