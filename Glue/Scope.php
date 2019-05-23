<?php


namespace PhpureCore\Glue;

use PhpureCore\Core\Glue;

/**
 * Class Scope
 *
 * @method static \PhpureCore\Config\Scope middleware($call, string $action = null, bool $isTail = false)
 * @method static \PhpureCore\Config\Scope post(string $key, $call, string $action = 'post')
 * @method static \PhpureCore\Config\Scope get(string $key, $call, string $action = 'get')
 * @method static \PhpureCore\Config\Scope put(string $key, $call, string $action = 'put')
 * @method static \PhpureCore\Config\Scope delete(string $key, $call, string $action = 'delete')
 * @method static \PhpureCore\Config\Scope patch(string $key, $call, string $action = 'patch')
 *
 * @see \PhpureCore\Config\Scope
 */
class Scope extends Glue
{
}