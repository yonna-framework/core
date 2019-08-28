<?php

namespace Yonna\Console;

use Exception;
use Workerman\Connection\TcpConnection;
use Yonna\Core;
use Yonna\Bootstrap\BootType;
use Yonna\Response\Collector;

/**
 * Class WorkermanHttp
 * @package Yonna\Console
 */
class WorkermanHttp extends Console
{

    private $worker = null;
    private $root_path = null;
    private $options = null;

    /**
     * \Workerman\Worker constructor.
     * @param $root_path
     * @param $options
     * @throws Exception
     */
    public function __construct($root_path, $options)
    {
        if (!class_exists('\Workerman\Worker')) {
            throw new Exception('class  Workerman\Worker not exists');
        }
        $this->root_path = $root_path;
        $this->options = $options;
        $this->checkParams($this->options, ['p', 'e']);
        return $this;
    }

    public function run()
    {
        $this->worker = new \Workerman\Worker("http://0.0.0.0:{$this->options['p']}");

        $this->worker->count = 4;

        $this->worker->onMessage = function (TcpConnection $connection, $request) {
            $responseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::WORKERMAN_HTTP,
                array(
                    'connection' => $connection,
                    'request' => $request,
                )
            );
            if ($responseCollector instanceof Collector) {
                $responseHeader = $responseCollector->getHeader('arr');
                foreach ($responseHeader as $hk => $hv) {
                    \Workerman\Protocols\Http::header($hk . ':' . $hv);
                }
                $connection->send($responseCollector->response());
            } else {
                $connection->send('response error');
            }
        };

        \Workerman\Worker::runAll();
    }
}