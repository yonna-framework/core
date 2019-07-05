<?php

namespace Yonna\Middleware;

use Closure;
use Yonna\Core;
use Yonna\Exception\Exception;
use Yonna\IO\Request;

class After extends Middleware
{

    private static $after = [];

    /**
     * @var Request
     */
    private $request = null;

    /**
     * @var mixed
     */
    private $response = null;

    /**
     * After constructor.
     * @param Request $request
     * @param mixed $response
     */
    public function __construct(Request $request, $response)
    {
        $this->request = $request;
        $this->response = $response;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * get middleware
     * @return string
     */
    public static function type(): string
    {
        return MiddlewareType::AFTER;
    }

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
                $call = function ($request, $response) use ($call) {
                    $After = Core::get($call, $request, $response);
                    if (!$After instanceof After) {
                        Exception::throw("Class {$call} is not instanceof Middleware-After");
                    }
                    $After->handle();
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
        return static::$after;
    }

    /**
     * 清空before
     */
    public static function clear()
    {
        static::$after = [];
    }

}