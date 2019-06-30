<?php

namespace Yonna\Glue;

use Yonna\Core\Glue;

/**
 * Class Response
 *
 * @method static \Yonna\IO\ResponseCollector success(string $msg = 'success', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\ResponseCollector broadcast(string $msg = 'broadcast', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\ResponseCollector goon(string $msg = 'goon', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\ResponseCollector error(string $msg = 'error', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\ResponseCollector exception(string $msg = 'exception', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\ResponseCollector notPermission(string $msg = 'not permission', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\ResponseCollector notFound(string $msg = 'not found', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\ResponseCollector abort(string $msg = 'abort', array $data = array(), $type = 'json')
 *
 * @see \Yonna\IO\Response
 */
class Response extends Glue
{
}