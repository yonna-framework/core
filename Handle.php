<?php
/**
 * Bootstrap handle
 */

namespace PhpureCore;

use PhpureCore\Handle\HandleCode;
use PhpureCore\Handle\HandleCollector;

/**
 * Class Handle
 * @package PhpureCore
 */
class Handle
{

    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @param array $extra
     */
    private static function handle(HandleCollector $handleCollector)
    {
        exit($handleCollector->response());
    }

    public static function success(string $message = 'success', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::SUCCES)
            ->setMessage($message)
            ->setData($data);
        self::handle($collector);
    }

    public static function broadcast(string $message = 'broadcast', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::BROADCAST)
            ->setMessage($message)
            ->setData($data);
        self::handle($collector);
    }

    public static function goon(string $message = 'goon', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::GOON)
            ->setMessage($message)
            ->setData($data);
        self::handle($collector);
    }

    public static function error(string $message = 'error', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::ERROR)
            ->setMessage($message)
            ->setData($data);
        self::handle($collector);
    }

    public static function exception(string $message = 'exception', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::EXCEPTION)
            ->setMessage($message)
            ->setData($data)
            ->setExtra(array(
                'debug_backtrace' => debug_backtrace()
            ));
        self::handle($collector);
    }

    public static function notPermission(string $message = 'not permission', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::NOT_PERMISSION)
            ->setMessage($message)
            ->setData($data);
        self::handle($collector);
    }

    public static function notFound(string $message = 'not found', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::NOT_FOUND)
            ->setMessage($message)
            ->setData($data);
        self::handle($collector);
    }

    public static function abort(string $message = 'abort', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::ABORT)
            ->setMessage($message)
            ->setData($data);
        self::handle($collector);
    }

}