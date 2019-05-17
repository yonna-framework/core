<?php
/**
 * Bootstrap handle code
 */

namespace PhpureCore\Handle;

use PhpureCore\Mapping;

/**
 * Class HandleCode
 * @package PhpureCore\Handle
 */
class HandleCode extends Mapping
{

    const SUCCESS = 200;
    const BROADCAST = 201;
    const GOON = 202;
    const EXCEPTION = 400;
    const ERROR = 401;
    const NOT_PERMISSION = 403;
    const NOT_FOUND = 404;
    const ABORT = 405;

}