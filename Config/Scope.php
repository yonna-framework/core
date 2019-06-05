<?php

namespace PhpureCore\Config;

use Closure;
use PhpureCore\Core;
use PhpureCore\Exception\Exception;
use PhpureCore\Scope\Neck;
use PhpureCore\Scope\Tail;
use PhpureCore\Glue\Response;

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
        if (empty($call)) Exception::throw('no call');
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
                    return Core::get($call, $request)->$action();
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
     * @param bool $isTail
     * @return $this
     */
    public function middleware($call, bool $isTail = false)
    {
        $isTail ? Tail::add($call) : Neck::add($call);
        return $this;
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

}