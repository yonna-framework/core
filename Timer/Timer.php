<?php
/**
 * timer 计时器
 */

namespace Yonna\Timer;

use Closure;
use Swoole\Timer as SwTimer;
use Yonna\Throwable\Exception;

/**
 * Class Timer
 * @package Yonna\Timer
 */
class Timer
{

    /**
     * 计时器集
     * @var array
     */
    private $timer = [];

    /**
     * 定时任务集合
     * @var array
     */
    private $cron = [];

    /**
     * Timer constructor.
     */
    public function __construct()
    {
        if (!class_exists(SwTimer::class)) {
            Exception::throw('Swoole\Timer is not exist');
        }
    }

    /**
     * @return array
     */
    public function getTimer(): array
    {
        return $this->timer;
    }

    /**
     * @param array $timer
     */
    public function setTimer(array $timer): void
    {
        $this->timer = $timer;
    }

    /**
     * @return array
     */
    public function getCron(): array
    {
        return $this->cron;
    }

    /**
     * @param array $cron
     */
    public function setCron(array $cron): void
    {
        $this->cron = $cron;
    }



    /**
     * 清除单个计时器
     * @return bool
     */
    public function clearAll(): bool
    {
        $this->timer = [];
        return SwTimer::clearAll();
    }

    /**
     * 清除单个计时器
     * @param int $timerId
     * @return bool
     */
    public function clear(int $timerId): bool
    {
        $index = array_search($timerId, $this->timer);
        array_splice($this->timer, $index, 1);
        return SwTimer::clear($timerId);
    }

    /**
     * 获取计时器信息
     * @param int $timerId
     * @return null
     */
    public function info(int $timerId)
    {
        if (!in_array($timerId, $this->timer)) {
            return null;
        }
        $info = SwTimer::info($timerId);
        if ($info['removed']) {
            $this->clear($timerId);
            $info = null;
        }
        return $info;
    }

    /**
     * 获取统计信息
     * @return array
     */
    public function stats(): array
    {
        return SwTimer::stats();
    }

    /**
     * 一次性延迟执行
     * @param int $microsecond
     * @param Closure $call
     * @return int timer-id
     */
    public function once(int $microsecond, Closure $call): int
    {
        $timerId = SwTimer::after($microsecond, $call);
        $this->timer[] = $timerId;
        return $timerId;
    }


    public function cron(int $microsecond, Closure $call)
    {

    }

}