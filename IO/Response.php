<?php
/**
 * IO Response
 */

namespace PhpureCore\IO;

use PhpureCore\Core;
use PhpureCore\Mapping\ResponseCode;

/**
 * Class Response
 * @package PhpureCore
 */
class Response
{

    public function __construct()
    {
        return $this;
    }

    /**
     * @param $Collector
     * @return false|string
     */
    public function handle(ResponseCollector $Collector)
    {
        return $Collector->response();
    }

    /**
     * @param ResponseCollector | string $data
     * @return false|string
     */
    public function end($data)
    {
        if ($data instanceof ResponseCollector) {
            switch ($data->getResponseDataType()) {
                case 'xml':
                    header('Content-Type:application/xml; charset=utf-8');
                    break;
                case 'json':
                    header('Content-Type:application/json; charset=utf-8');
                    break;
                case 'html':
                    header('Content-Type:text/html; charset=utf-8');
                    break;
                default:
                    header('Content-Type:text/plain; charset=utf-8');
                    break;
            }
            exit($data->response());
        } else if (is_array($data)) {
            exit($data);
        }
        exit('Not result');
    }

    public function success(string $msg = 'success', array $data = array(), $type = 'json')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(ResponseCode::SUCCESS)
            ->setMsg($msg)
            ->setData($data);
        return $HandleCollector;
    }

    public function broadcast(string $msg = 'broadcast', array $data = array(), $type = 'json')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(ResponseCode::BROADCAST)
            ->setMsg($msg)
            ->setData($data);
        return $HandleCollector;
    }

    public function goon(string $msg = 'goon', array $data = array(), $type = 'json')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(ResponseCode::GOON)
            ->setMsg($msg)
            ->setData($data);
        return $HandleCollector;
    }

    public function error(string $msg = 'error', array $data = array(), $type = 'json')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(ResponseCode::ERROR)
            ->setMsg($msg)
            ->setData($data);
        return $HandleCollector;
    }

    public function exception(string $msg = 'exception', array $data = array(), $type = 'json')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(ResponseCode::EXCEPTION)
            ->setMsg($msg)
            ->setData($data)
            ->setExtra(array(
                'debug_backtrace' => debug_backtrace()
            ));
        $this->end($HandleCollector);
    }

    public function notPermission(string $msg = 'not permission', array $data = array(), $type = 'json')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(ResponseCode::NOT_PERMISSION)
            ->setMsg($msg)
            ->setData($data);
        $this->end($HandleCollector);
    }

    public function notFound(string $msg = 'not found', array $data = array(), $type = 'json')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(ResponseCode::NOT_FOUND)
            ->setMsg($msg)
            ->setData($data);
        $this->end($HandleCollector);
    }

    public function abort(string $msg = 'abort', array $data = array(), $type = 'json')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(ResponseCode::ABORT)
            ->setMsg($msg)
            ->setData($data);
        $this->end($HandleCollector);
    }

}