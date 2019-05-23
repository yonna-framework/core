<?php

namespace PhpureCore\Config;

use Closure;
use PhpureCore\Core;
use PhpureCore\Scope\Neck;
use PhpureCore\Scope\Tail;
use PhpureCore\Glue\Handle;

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
        if (empty($method)) Handle::exception('no method');
        if (empty($key)) Handle::exception('no key');
        if (empty($call)) Handle::exception('no call');
        // upper
        $method = strtoupper($method);
        $key = strtoupper($key);
        if (!isset(static::$stack[self::name][$method])) {
            static::$stack[self::name][$method] = [];
        }
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                !$action && Handle::exception("Should call a action for {$call}");
                $call = function ($request) use ($call, $action) {
                    Core::get($call, $request)->$action();
                };
            }
        }
        // if call instanceof Closure, combine the neck and tail
        if ($call instanceof Closure) {
            if (!isset(static::$stack[self::name][$method][$key])) {
                static::$stack[self::name][$method][$key] = ['neck' => [], 'call' => null, 'tail' => []];
            }
            // neck
            $necks = Neck::fetch();
            if ($necks) foreach ($necks as $neck) static::$stack[self::name][$method][$key]['neck'][] = $neck;
            // call
            static::$stack[self::name][$method][$key]['call'] = $call;
            // neck
            $tails = Tail::fetch();
            if ($tails) foreach ($tails as $tail) static::$stack[self::name][$method][$key]['tail'][] = $tail;
        }
    }

    /**
     * middleware
     * @param $call
     * @param string|null $action
     * @param bool $isTail
     * @return $this
     */
    public function middleware($call, string $action = null, bool $isTail = false)
    {
        if (is_string($call)) {
            $call = [[$call, $action]];
        }
        foreach ($call as $c) {
            list($class, $act) = $c;
            $isTail ? Tail::add($call, $action) : Neck::add($class, $act);
        }
        return $this;
    }

    /**
     * POST
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     * @return $this
     */
    public function post(string $key, $call, string $action = 'post')
    {
        $this->add('post', $key, $call, $action);
        return $this;
    }

    /**
     * GET
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     * @return $this
     */
    public function get(string $key, $call, string $action = 'get')
    {
        $this->add('get', $key, $call, $action);
        return $this;
    }

    /**
     * PUT
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     * @return $this
     */
    public function put(string $key, $call, string $action = 'put')
    {
        $this->add('put', $key, $call, $action);
        return $this;
    }

    /**
     * DELETE
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     * @return $this
     */
    public function delete(string $key, $call, string $action = 'delete')
    {
        $this->add('delete', $key, $call, $action);
        return $this;
    }

    /**
     * PATCH
     * @param string $key
     * @param Closure | string $call
     * @param string $action
     * @return $this
     */
    public function patch(string $key, $call, string $action = 'patch')
    {
        $this->add('patch', $key, $call, $action);
        return $this;
    }

}