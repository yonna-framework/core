<?php

namespace Yonna\Middleware;

use Closure;
use Yonna\Core;
use Yonna\Response\Response;
use Yonna\Throwable\Exception;
use Yonna\IO\Request;

class After extends Middleware
{

    protected static $type = MiddlewareType::AFTER;
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
     * handle
     * @return Response
     */
    public function handle(): Response
    {
    }

    /**
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * 添加 after
     * @param Closure | string $call
     * @throws null
     */
    public static function add($call)
    {
        if (empty($call)) Exception::throw('no call class');
        // if call instanceof string, convert it to Closure
        if (is_string($call)) {
            if (class_exists($call)) {
                $call = function ($request, $response) use ($call): Response {
                    $After = Core::get($call, $request, $response);
                    if (!$After instanceof After) {
                        Exception::throw("Class {$call} is not instanceof Middleware-After");
                    }
                    return $After->handle();
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