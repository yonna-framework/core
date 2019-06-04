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


    public function handle()
    {
        // your handle
    }

}