<?php
/**
 * timer 计时器
 */

namespace Yonna\Timer;

use Swoole\Timer as SwTimer;
use Yonna\Throwable\Exception;

/**
 * Class Timer
 * @package Yonna\Timer
 */
class Timer
{

    /**
     * 计时器集合
     * @var array
     */
    private $timers = [];

    public function __construct()
    {
        if (!class_exists(SwTimer::class)) {
            Exception::throw('Swoole\Timer is not exist');
        }
    }

    public function once()
    {

    }


    public function cron()
    {

    }

}