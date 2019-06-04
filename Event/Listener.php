<?php

namespace PhpureCore\Event;

/**
 * Class Listener
 * @package PhpureCore\Event
 */
abstract class Listener
{

    private $event = null;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * 全部的listener都要集成的 handle 方法，会被自动调用
     * auto call handle
     */
    public function handle()
    {
        // your handle
    }

    /**
     * 获取事件对象
     * @return Event|null
     */
    protected function getEvent()
    {
        return $this->event;
    }

    /**
     * 获取事件对象
     * @return Event|null
     */
    protected function getParams()
    {
        return $this->getEvent()->getParams();
    }


}