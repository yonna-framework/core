<?php

namespace PhpureCore;

class Interfaces
{
    const default = [
        \PhpureCore\Interfaces\Bootstrap::class => \PhpureCore\Bootstrap::class,
        \PhpureCore\Interfaces\Cargo::class => \PhpureCore\Bootstrap\Cargo::class,
        \PhpureCore\Interfaces\Request::class => \PhpureCore\IO\Request::class,
    ];

    public static function get(): array
    {
        $interfaces = self::default;

        return $interfaces;
    }

}