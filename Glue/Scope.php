<?php


namespace Yonna\Glue;

use Yonna\Core\Glue;

/**
 * Class Scope
 *
 * @method static \Yonna\Config\Scope middleware($call, \Closure $closure)
 * @method static post(string $key, $call, string $action = 'post')
 * @method static get(string $key, $call, string $action = 'get')
 * @method static put(string $key, $call, string $action = 'put')
 * @method static delete(string $key, $call, string $action = 'delete')
 * @method static patch(string $key, $call, string $action = 'patch')
 *
 * @see \Yonna\Config\Scope
 */
class Scope extends Glue
{
}