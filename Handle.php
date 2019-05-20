<?php
/**
 * Bootstrap handle
 */

namespace PhpureCore;

use PhpureCore\Mapping\HandleCode;
use PhpureCore\Handle\HandleCollector;

/**
 * Class Handle
 * @package PhpureCore
 */
class Handle
{

    /**
     * @param HandleCollector $handleCollector
     * @return false|string
     */
    private static function handle(HandleCollector $handleCollector)
    {
        return $handleCollector->response();
    }

    /**
     * @param HandleCollector | string $data
     * @return false|string
     */
    public static function end($data)
    {
        if ($data instanceof HandleCollector) {
            switch ($data->getResponseDataType()) {
                case 'xml':
                    header('Content-Type:application/xml; charset=utf-8');
                    break;
                case 'json':
                    header('Content-Type:application/json; charset=utf-8');
                    break;
                default:
                    header('Content-Type:text/html; charset=utf-8');
                    break;
            }
            exit($data->response());
        } else if (is_array($data)) {
            exit($data);
        }
        exit('Not result');
    }

    public static function success(string $message = 'success', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::SUCCESS)
            ->setMessage($message)
            ->setData($data);
        return self::handle($collector);
    }

    public static function broadcast(string $message = 'broadcast', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::BROADCAST)
            ->setMessage($message)
            ->setData($data);
        return self::handle($collector);
    }

    public static function goon(string $message = 'goon', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::GOON)
            ->setMessage($message)
            ->setData($data);
        return self::handle($collector);
    }

    public static function error(string $message = 'error', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::ERROR)
            ->setMessage($message)
            ->setData($data);
        return self::handle($collector);
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
        self::end($collector);
    }

    public static function notPermission(string $message = 'not permission', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::NOT_PERMISSION)
            ->setMessage($message)
            ->setData($data);
        self::end($collector);
    }

    public static function notFound(string $message = 'not found', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::NOT_FOUND)
            ->setMessage($message)
            ->setData($data);
        self::end($collector);
    }

    public static function abort(string $message = 'abort', array $data = array(), $type = 'json')
    {
        $collector = (new HandleCollector())
            ->setResponseDataType($type)
            ->setCode(HandleCode::ABORT)
            ->setMessage($message)
            ->setData($data);
        self::end($collector);
    }

}