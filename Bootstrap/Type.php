<?php
/**
 * Bootstrap Types
 */

namespace PhpureCore\Bootstrap;

use PhpureCore\Mapping;

class Type extends Mapping
{

    const AJAX_HTTP = 'AJAX_HTTP';
    const SWOOLE_HTTP = 'SWOOLE_HTTP';
    const SWOOLE_WEB_SOCKET = 'SWOOLE_WEB_SOCKET';
    const SWOOLE_TCP = 'SWOOLE_TCP';

}