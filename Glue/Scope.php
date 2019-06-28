<?php


namespace PhpureCore\Glue;

use PhpureCore\Core\Glue;

/**
 * Class Scope
 *
 * @method static \PhpureCore\Config\Scope middleware($call, \Closure $closure)
 * @method static post(string $key, $call, string $action = 'post')
 * @method static get(string $key, $call, string $action = 'get')
 * @method static put(string $key, $call, string $action = 'put')
 * @method static delete(string $key, $call, string $action = 'delete')
 * @method static patch(string $key, $call, string $action = 'patch')
 *
 * @see \PhpureCore\Config\Scope
 */
class Scope extends Glue
{
}