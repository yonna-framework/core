<?php

namespace Yonna\Glue;

use Yonna\Core\Glue;

/**
 * Class Response
 *
 * @method static \Yonna\IO\Collector success(string $msg = 'success', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\Collector broadcast(string $msg = 'broadcast', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\Collector goon(string $msg = 'goon', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\Collector error(string $msg = 'error', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\Collector exception(string $msg = 'exception', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\Collector notPermission(string $msg = 'not permission', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\Collector notFound(string $msg = 'not found', array $data = array(), $type = 'json')
 * @method static \Yonna\IO\Collector abort(string $msg = 'abort', array $data = array(), $type = 'json')
 *
 * @see \Yonna\IO\Response
 */
class Response extends Glue
{
}