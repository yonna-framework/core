<?php

namespace PhpureCore\Container;

class Interfaces
{
    const default = [
        \PhpureCore\Interfaces\Bootstrap::class => \PhpureCore\Bootstrap\Bootstrap::class,
        \PhpureCore\Interfaces\Request::class => \PhpureCore\IO\Request::class,
        \PhpureCore\Interfaces\IO::class => \PhpureCore\IO\IO::class,
    ];

    public static function get(): array
    {
        $interfaces = self::default;

        return $interfaces;
    }

}