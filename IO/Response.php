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
     * safety debug backtrace
     * @param int $safeLv 安全等级，数字越高安全性越高
     * @return array
     */
    private function debug_backtrace($safeLv = 0)
    {
        $path = realpath(__DIR__ . '/../../../..');
        $trace = debug_backtrace();
        foreach ($trace as $tk => $t) {
            if ($safeLv >= 3) {
                if (isset($t['line'])) unset($trace[$tk]['line']);
            }
            if ($safeLv >= 2) {
                if (isset($t['type'])) unset($trace[$tk]['type']);
            }
            if ($safeLv >= 1) {
                if (isset($t['object'])) unset($trace[$tk]['object']);
                if (isset($t['args'])) unset($trace[$tk]['args']);
                if (!empty($t['file'])) {
                    $trace[$tk]['file'] = str_replace($path, '#:Pure', str_replace(
                        'vendor' . DIRECTORY_SEPARATOR . 'hunzsig-server' . DIRECTORY_SEPARATOR . 'phpure-core',
                        'C',
                        $t['file']
                    ));
                }
            }
        }
        return $trace;
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
            ->setExtra(array('debug_backtrace' => getenv('IS_DEBUG') === 'true'
                ? $this->debug_backtrace(0) : $this->debug_backtrace(1),
            ));
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
            ->setExtra(array('debug_backtrace' => getenv('IS_DEBUG') === 'true'
                ? $this->debug_backtrace(1) : $this->debug_backtrace(2),
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
            ->setData($data)
            ->setExtra(array('debug_backtrace' => getenv('IS_DEBUG') === 'true'
                ? $this->debug_backtrace(2) : $this->debug_backtrace(3),
            ));
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
            ->setExtra(array('debug_backtrace' => getenv('IS_DEBUG') === 'true'
                ? $this->debug_backtrace(2) : $this->debug_backtrace(3),
            ));
        $this->end($HandleCollector);
    }

}