<?php
/**
 * timer 计时器
 */

namespace Yonna\Timer;

use Closure;
use Swoole\Timer as SwTimer;

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
    private static $timers = [];

    /**
     * 定时任务集合
     * @var array
     */
    private static $crons = [];

    /**
     * @return array
     */
    public static function getTimers(): array
    {
        return self::$timers;
    }

    /**
     * @param array $timers
     */
    public static function setTimers(array $timers): void
    {
        self::$timers = $timers;
    }

    /**
     * @return array
     */
    public static function getCron(): array
    {
        return self::$crons;
    }

    /**
     * @param array $cron
     */
    public static function setCron(array $cron): void
    {
        self::$crons = $cron;
    }


    /**
     * 清除单个计时器
     * @return bool
     */
    public static function clearAll(): bool
    {
        self::$timers = [];
        return SwTimer::clearAll();
    }

    /**
     * 清除单个计时器
     * @param int $timerId
     * @return bool
     */
    public static function clear(int $timerId): bool
    {
        $index = array_search($timerId, self::$timers);
        array_splice(self::$timers, $index, 1);
        return SwTimer::clear($timerId);
    }

    /**
     * 获取计时器信息
     * @param int $timerId
     * @return null
     */
    public static function info(int $timerId)
    {
        if (!in_array($timerId, self::$timers)) {
            return null;
        }
        $info = SwTimer::info($timerId);
        if ($info['removed']) {
            self::clear($timerId);
            $info = null;
        }
        return $info;
    }

    /**
     * 获取统计信息
     * @return array
     */
    public static function stats(): array
    {
        return SwTimer::stats();
    }

    /**
     * 一次性延迟执行
     * @param int $microsecond
     * @param Closure $call
     * @return int timer-id
     */
    public static function once(int $microsecond, Closure $call): int
    {
        $timerId = SwTimer::after($microsecond, $call);
        self::$timers[] = $timerId;
        return $timerId;
    }


    /**
     * 定时任务
     * @param int $microsecond
     * @param Closure $call
     * @param $params
     */
    public static function cron(int $microsecond, Closure $call, ... $params)
    {
        if (!isset(self::$crons[$microsecond])) {
            SwTimer::tick($microsecond, function (int $timer_id, ... $params) use ($microsecond) {
                if (empty(Timer::$crons[$microsecond])) {
                    Timer::clear($timer_id);
                    return;
                }
                foreach (Timer::$crons[$microsecond] as $c) {
                    $c($params);
                }
            }, $params);
            self::$crons[$microsecond] = [];
        }
        self::$crons[$microsecond][] = $call;
    }

}