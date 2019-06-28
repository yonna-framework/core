<?php

namespace PhpureCore\Scope\Interfaces;

/**
 * Interface Middleware
 * @package PhpureCore\Interfaces
 */
interface Middleware
{

    public static function type(): string;

    public function handle($params);

}

