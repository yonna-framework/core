<?php

namespace PhpureCore\Glue;

use PhpureCore\Core\Glue;

/**
 * Class Response
 *
 * @method static \PhpureCore\IO\ResponseCollector success(string $msg = 'success', array $data = array(), $type = 'json')
 * @method static \PhpureCore\IO\ResponseCollector broadcast(string $msg = 'broadcast', array $data = array(), $type = 'json')
 * @method static \PhpureCore\IO\ResponseCollector goon(string $msg = 'goon', array $data = array(), $type = 'json')
 * @method static \PhpureCore\IO\ResponseCollector error(string $msg = 'error', array $data = array(), $type = 'json')
 * @method static \PhpureCore\IO\ResponseCollector exception(string $msg = 'exception', array $data = array(), $type = 'json')
 * @method static \PhpureCore\IO\ResponseCollector notPermission(string $msg = 'not permission', array $data = array(), $type = 'json')
 * @method static \PhpureCore\IO\ResponseCollector notFound(string $msg = 'not found', array $data = array(), $type = 'json')
 * @method static \PhpureCore\IO\ResponseCollector abort(string $msg = 'abort', array $data = array(), $type = 'json')
 *
 * @see \PhpureCore\IO\Response
 */
class Response extends Glue
{
}