<?php

namespace PhpureCore\Config;

use Closure;
use PhpureCore\Core;
use PhpureCore\Glue\Response;

class Trigger extends Arrow
{

    const name = 'trigger';

    /**
     * 注册触发器,设定需要的参数要求
     * @param string $eventClass
     * @param array $listenerClasses
     */
    public static function reg(string $eventClass, array $listenerClasses)
    {
        if (empty($eventClass)) Response::exception('not event');
        if (empty($listenerClasses)) Response::exception('not listener');
        if (!empty(self::$stack[self::name][$eventClass])) {
            Response::abort("Event {$eventClass} already exist");
        }
        self::$stack[self::name][$eventClass] = $listenerClasses;
    }

    /**
     * 删除触发器
     * @param string $eventClass
     */
    public static function del(string $eventClass)
    {
        if (empty($eventClass)) Response::exception('not event');
        if (isset(self::$stack[self::name][$eventClass])) {
            unset(self::$stack[self::name][$eventClass]);
        }
    }

    /**
     * 触发触发器
     * @param string $eventClass
     * @param $params
     */
    public static function act(string $eventClass, $params)
    {
        if (empty($eventClass)) Response::exception('not event');
        if (empty(self::$stack[self::name][$eventClass])) {
            return;
        }
        /**
         * @var \PhpureCore\Event\Event $eventClass
         */
        $eventClass = Core::get($eventClass, $params);
        $eventClass->listener(self::$stack[self::name][$eventClass]);
        if (is_array(self::$stack[self::name][$eventClass])) {
            foreach (self::$stack[self::name][$eventClass] as $eIdx => $call) {
                if ($call instanceof Closure) {
                    $call($params[$eIdx] ?? null);
                }
            }
        }
    }

}