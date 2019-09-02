<?php

namespace Yonna\Console;

use Exception;
use Workerman\Connection\TcpConnection;
use Yonna\Core;
use Yonna\Bootstrap\BootType;
use Yonna\IO\RequestBuilder;
use Yonna\Response\Collector;

/**
 * Class WorkermanWebsocket
 * @package Yonna\Console
 */
class WorkermanWebsocket extends Console
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

    /**
     * build a request
     * @param mixed ...$options
     * @return RequestBuilder
     */
    private function requestBuilder(...$options): RequestBuilder
    {
        $connection = $options[0];
        $message = $options[1];
        $client_id = BootType::WORKERMAN_WEB_SOCKET . '#' . $connection->worker_id;
        /**
         * @var RequestBuilder $requestBuilder
         */
        $requestBuilder = Core::get(RequestBuilder::class);
        $requestBuilder->setHttpXRealIp($connection->getRemoteIp());
        $requestBuilder->setHttpClientIp($connection->getRemoteIp());
        $requestBuilder->setRemoteAddr($connection->getRemoteIp());
        $requestBuilder->setRemotePort($connection->getRemotePort());
        $requestBuilder->setHttpXHost($connection->getRemoteIp() . ":" . $connection->getRemotePort());
        $requestBuilder->setRequestMethod('STREAM');
        $requestBuilder->setContentType('application/json');
        $requestBuilder->setClientId($client_id);
        $requestBuilder->setHttpUserAgent($client_id);
        $requestBuilder->setRawData($message ?? '');
        return $requestBuilder;
    }

    /**
     *
     */
    public function run()
    {
        $this->worker = new \Workerman\Worker("websocket://0.0.0.0:{$this->options['p']}");

        $this->worker->count = 4;

        $this->worker->onMessage = function (TcpConnection $connection, $message) {
            $responseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::WORKERMAN_WEB_SOCKET,
                $this->requestBuilder($connection, $message)
            );
            if ($responseCollector instanceof Collector) {
                $connection->send($responseCollector->response());
            } else {
                $connection->send('response error');
            }
        };

        \Workerman\Worker::runAll();
    }
}