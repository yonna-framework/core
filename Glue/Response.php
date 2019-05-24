<?php

namespace PhpureCore\Glue;

use PhpureCore\Core\Glue;

/**
 * Class Response
 *
 * @method static end($data)
 * @method static success(string $msg = 'success', array $data = array(), $type = 'json')
 * @method static broadcast(string $msg = 'broadcast', array $data = array(), $type = 'json')
 * @method static goon(string $msg = 'goon', array $data = array(), $type = 'json')
 * @method static error(string $msg = 'error', array $data = array(), $type = 'json')
 * @method static exception(string $msg = 'exception', array $data = array(), $type = 'json')
 * @method static notPermission(string $msg = 'not permission', array $data = array(), $type = 'json')
 * @method static notFound(string $msg = 'not found', array $data = array(), $type = 'json')
 * @method static abort(string $msg = 'abort', array $data = array(), $type = 'json')
 *
 * @see \PhpureCore\IO\Response
 */
class Response extends Glue
{
}