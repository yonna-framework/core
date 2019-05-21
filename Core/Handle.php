<?php
/**
 * Bootstrap handle
 */

namespace PhpureCore\Core;

use PhpureCore\Core;
use PhpureCore\Mapping\HandleCode;

/**
 * Class Handle
 * @package PhpureCore
 */
class Handle
{

    public function __construct()
    {
        return $this;
    }

    /**
     * @param $Collector
     * @return false|string
     */
    private function handle(HandleCollector $Collector)
    {
        return $Collector->response();
    }

    /**
     * @param HandleCollector | string $data
     * @return false|string
     */
    public function end($data)
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

    public function success(string $message = 'success', array $data = array(), $type = 'json')
    {
        /** @var HandleCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\HandleCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(HandleCode::SUCCESS)
            ->setMessage($message)
            ->setData($data);
        return $this->handle($HandleCollector);
    }

    public function broadcast(string $message = 'broadcast', array $data = array(), $type = 'json')
    {
        /** @var HandleCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\HandleCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(HandleCode::BROADCAST)
            ->setMessage($message)
            ->setData($data);
        return $this->handle($HandleCollector);
    }

    public function goon(string $message = 'goon', array $data = array(), $type = 'json')
    {
        /** @var HandleCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\HandleCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(HandleCode::GOON)
            ->setMessage($message)
            ->setData($data);
        return $this->handle($HandleCollector);
    }

    public function error(string $message = 'error', array $data = array(), $type = 'json')
    {
        /** @var HandleCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\HandleCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(HandleCode::ERROR)
            ->setMessage($message)
            ->setData($data);
        return $this->handle($HandleCollector);
    }

    public function exception(string $message = 'exception', array $data = array(), $type = 'json')
    {
        /** @var HandleCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\HandleCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(HandleCode::EXCEPTION)
            ->setMessage($message)
            ->setData($data)
            ->setExtra(array(
                'debug_backtrace' => debug_backtrace()
            ));
        $this->end($HandleCollector);
    }

    public function notPermission(string $message = 'not permission', array $data = array(), $type = 'json')
    {
        /** @var HandleCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\HandleCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(HandleCode::NOT_PERMISSION)
            ->setMessage($message)
            ->setData($data);
        $this->end($HandleCollector);
    }

    public function notFound(string $message = 'not found', array $data = array(), $type = 'json')
    {
        /** @var HandleCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\HandleCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(HandleCode::NOT_FOUND)
            ->setMessage($message)
            ->setData($data);
        $this->end($HandleCollector);
    }

    public function abort(string $message = 'abort', array $data = array(), $type = 'json')
    {
        /** @var HandleCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\HandleCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCode(HandleCode::ABORT)
            ->setMessage($message)
            ->setData($data);
        $this->end($HandleCollector);
    }

}