<?php

namespace PhpureCore\Glue;

use PhpureCore\Core\Glue;

/**
 * Class Response
 *
 * @method static end($data)
 * @method static success(string $message = 'success', array $data = array(), $type = 'json')
 * @method static broadcast(string $message = 'broadcast', array $data = array(), $type = 'json')
 * @method static goon(string $message = 'goon', array $data = array(), $type = 'json')
 * @method static error(string $message = 'error', array $data = array(), $type = 'json')
 * @method static exception(string $message = 'exception', array $data = array(), $type = 'json')
 * @method static notPermission(string $message = 'not permission', array $data = array(), $type = 'json')
 * @method static notFound(string $message = 'not found', array $data = array(), $type = 'json')
 * @method static abort(string $message = 'abort', array $data = array(), $type = 'json')
 *
 * @see \PhpureCore\IO\Response
 */
class Handle extends Glue
{
}