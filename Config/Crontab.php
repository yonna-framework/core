<?php

namespace PhpureCore\Config;

class Crontab extends Arrow
{

    const name = 'crontab';

    public function __construct()
    {
        if (!isset(self::$stack[self::name])) {
            self::$stack[self::name] = array();
        }
        return $this;
    }

}