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
     * @param null $trace
     * @return array
     */
    private function debug_backtrace($safeLv = 0, $trace = null)
    {
        $path = realpath(__DIR__ . '/../../../..');
        if (empty($trace)) $trace = debug_backtrace();
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
                    header('Content-Type:application/xml; charset=' . $data->getCharset());
                    break;
                case 'json':
                    header('Content-Type:application/json; charset=' . $data->getCharset());
                    break;
                case 'html':
                    header('Content-Type:text/html; charset=' . $data->getCharset());
                    break;
                default:
                    header('Content-Type:text/plain; charset=' . $data->getCharset());
                    break;
            }
            exit($data->response());
        } else if (is_array($data)) {
            exit($data);
        }
        exit('Not result');
    }

    public function success(string $msg = 'success', array $data = array(), $type = 'json', $charset = 'utf-8')
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

    public function broadcast(string $msg = 'broadcast', array $data = array(), $type = 'json', $charset = 'utf-8')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCharset($charset)
            ->setCode(ResponseCode::BROADCAST)
            ->setMsg($msg)
            ->setData($data);
        return $HandleCollector;
    }

    public function goon(string $msg = 'goon', array $data = array(), $type = 'json', $charset = 'utf-8')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCharset($charset)
            ->setCode(ResponseCode::GOON)
            ->setMsg($msg)
            ->setData($data);
        return $HandleCollector;
    }

    public function error(string $msg = 'error', array $data = array(), $type = 'json', $charset = 'utf-8')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCharset($charset)
            ->setCode(ResponseCode::ERROR)
            ->setMsg($msg)
            ->setData($data);
        return $HandleCollector;
    }

    public function exception(string $msg = 'exception', array $data = array(), $type = 'json', $charset = 'utf-8')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCharset($charset)
            ->setCode(ResponseCode::EXCEPTION)
            ->setMsg($msg)
            ->setData($data)
            ->setExtra(array('debug_backtrace' => getenv('IS_DEBUG') === 'true'
                ? $this->debug_backtrace(0, $data) : $this->debug_backtrace(1, $data),
            ));
        $this->end($HandleCollector);
    }

    public function abort(string $msg = 'abort', array $data = array(), $type = 'json', $charset = 'utf-8')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCharset($charset)
            ->setCode(ResponseCode::ABORT)
            ->setMsg($msg)
            ->setExtra(array('debug_backtrace' => getenv('IS_DEBUG') === 'true'
                ? $this->debug_backtrace(1, $data) : $this->debug_backtrace(2, $data),
            ));
        $this->end($HandleCollector);
    }

    public function notPermission(string $msg = 'not permission', array $data = array(), $type = 'json', $charset = 'utf-8')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCharset($charset)
            ->setCode(ResponseCode::NOT_PERMISSION)
            ->setMsg($msg)
            ->setData($data)
            ->setExtra(array('debug_backtrace' => getenv('IS_DEBUG') === 'true'
                ? $this->debug_backtrace(2, $data) : $this->debug_backtrace(3, $data),
            ));
        $this->end($HandleCollector);
    }

    public function notFound(string $msg = 'not found', array $data = array(), $type = 'json', $charset = 'utf-8')
    {
        /** @var ResponseCollector $HandleCollector */
        $HandleCollector = Core::get(\PhpureCore\Glue\ResponseCollector::class);
        $HandleCollector
            ->setResponseDataType($type)
            ->setCharset($charset)
            ->setCode(ResponseCode::NOT_FOUND)
            ->setMsg($msg)
            ->setExtra(array('debug_backtrace' => getenv('IS_DEBUG') === 'true'
                ? $this->debug_backtrace(2, $data) : $this->debug_backtrace(3, $data),
            ));
        $this->end($HandleCollector);
    }

}