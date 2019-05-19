<?php

namespace PhpureCore\Container;

class Interfaces
{
    const default = [
        \PhpureCore\Interfaces\Bootstrap::class => \PhpureCore\Bootstrap\Bootstrap::class,
        \PhpureCore\Interfaces\Request::class => \PhpureCore\IO\Request::class,
    ];

    public static function get(): array
    {
        $interfaces = self::default;

        return $interfaces;
    }

}