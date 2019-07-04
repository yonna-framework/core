<?php

namespace Yonna\Middleware;

/**
 * yonna auth是一个yonna自带的中间件
 *
 * Class YonnaAuth
 * @package Yonna\Middleware
 */
class YonnaAuth extends Before
{
    public function handle($params)
    {
        print_r($params);
    }

}