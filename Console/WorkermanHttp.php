<?php

namespace Yonna\Console;

use Exception;
use Workerman\Connection\TcpConnection;
use Yonna\Core;
use Yonna\Bootstrap\BootType;
use Yonna\IO\RequestBuilder;
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

    /**
     * build a request
     * @param mixed ...$options
     * @return RequestBuilder
     */
    private function requestBuilder(...$options): RequestBuilder
    {
        $connection = $options[0];
        $request = $options[1];
        /**
         * @var RequestBuilder $requestBuilder
         */
        $requestBuilder = Core::get(RequestBuilder::class);
        $requestBuilder->setHttpXRealIp($connection->getRemoteIp());
        $requestBuilder->setHttpClientIp($connection->getRemoteIp());
        $requestBuilder->setGet($request['get'] ?? []);
        $requestBuilder->setPost($request['post'] ?? []);
        $requestBuilder->setCookie($request['cookie'] ?? []);
        $requestBuilder->setFiles($request['files'] ?? []);
        $requestBuilder->setRawData($GLOBALS['HTTP_RAW_POST_DATA'] ?? '');
        $requestBuilder->setQueryString($request['server']['QUERY_STRING'] ?? '');
        $requestBuilder->setRequestMethod($request['server']['REQUEST_METHOD'] ?? '');
        $requestBuilder->setRequestUri($request['server']['REQUEST_URI'] ?? '');
        $requestBuilder->setServerProtocol($request['server']['SERVER_PROTOCOL'] ?? '');
        $requestBuilder->setServerSoftware($request['server']['SERVER_SOFTWARE'] ?? '');
        $requestBuilder->setServerName($request['server']['SERVER_NAME'] ?? '');
        $requestBuilder->setServerPort($request['server']['SERVER_PORT'] ?? '');
        $requestBuilder->setHttpHost($request['server']['HTTP_HOST'] ?? '');
        $requestBuilder->setHttpUserAgent($request['server']['HTTP_USER_AGENT'] ?? '');
        $requestBuilder->setHttpAccept($request['server']['HTTP_ACCEPT'] ?? '');
        $requestBuilder->setHttpAcceptLanguage($request['server']['HTTP_ACCEPT_LANGUAGE'] ?? '');
        $requestBuilder->setHttpAcceptEncoding($request['server']['HTTP_ACCEPT_ENCODING'] ?? '');
        $requestBuilder->setHttpConnection($request['server']['HTTP_CONNECTION'] ?? '');
        $requestBuilder->setContentLength($request['server']['CONTENT_LENGTH'] ?? 0);
        $requestBuilder->setContentType($request['server']['CONTENT_TYPE'] ?? '');
        $requestBuilder->setRemoteAddr($request['server']['REMOTE_ADDR'] ?? '');
        $requestBuilder->setRemotePort($request['server']['REMOTE_PORT'] ?? '');
        $requestBuilder->setRequestTime($request['server']['REQUEST_TIME'] ?? '');
        $requestBuilder->setClientId($request['server']['HTTP_POSTMAN_TOKEN'] ?? '');
        return $requestBuilder;
    }

    /**
     * run
     */
    public function run()
    {
        $this->worker = new \Workerman\Worker("http://0.0.0.0:{$this->options['p']}");

        $this->worker->count = 4;

        $this->worker->onMessage = function (TcpConnection $connection, $request) {
            $responseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::WORKERMAN_HTTP,
                $this->requestBuilder($connection, $request)
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