<?php

namespace Yonna\Console;

use Exception;
use Yonna\Core;
use Yonna\IO\RequestBuilder;
use Yonna\Response\Collector;
use Yonna\Bootstrap\BootType;
use swoole_websocket_server;

/**
 * swoole http
 * Class SwooleHttp
 */
class SwooleWebsocket extends Console
{

    private $server = null;
    private $root_path = null;
    private $options = null;
    private $clients = array();

    /**
     * SwooleHttp constructor.
     * @param $root_path
     * @param $options
     * @throws Exception
     */
    public function __construct($root_path, $options)
    {
        if (!class_exists('swoole_websocket_server')) {
            throw new Exception('class swoole_websocket_server not exists');
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
        $server = $options[0];
        $task_id = $options[1];
        $from_id = $options[2];
        $request = $options[3];

        /**
         * @var RequestBuilder $requestBuilder
         */
        $requestBuilder = Core::get(RequestBuilder::class);
        $requestBuilder->setSwoole($server);
        $requestBuilder->setHttpXRealIp($request['server']['remote_addr']);
        $requestBuilder->setHttpClientIp($request['server']['remote_addr']);
        $requestBuilder->setRemoteAddr($request['server']['remote_addr']);
        $requestBuilder->setGet($request['get'] ?? []);
        $requestBuilder->setPost($request['post'] ?? []);
        $requestBuilder->setRequest($request['request'] ?? []);
        $requestBuilder->setCookie($request['cookie'] ?? []);
        $requestBuilder->setFiles($request['files'] ?? []);
        $requestBuilder->setRawData($request['rawData'] ?? '');
        $requestBuilder->setPathInfo($request['server']['path_info'] ?? '');
        $requestBuilder->setQueryString($request['server']['query_string'] ?? '');
        $requestBuilder->setRequestMethod('STREAM');
        $requestBuilder->setRequestUri($request['server']['request_uri'] ?? '');
        $requestBuilder->setServerProtocol($request['server']['server_protocol'] ?? '');
        $requestBuilder->setServerSoftware($request['server']['server_software'] ?? '');
        $requestBuilder->setServerName($request['server']['server_name'] ?? '');
        $requestBuilder->setServerPort($request['server']['server_port'] ?? '');
        $requestBuilder->setRemoteAddr($request['server']['remote_addr'] ?? '');
        $requestBuilder->setRemotePort($request['server']['remote_port'] ?? '');
        $requestBuilder->setRequestTime($request['server']['request_time'] ?? '');
        $requestBuilder->setRequestTimeFloat($request['server']['request_time_float'] ?? '');
        $requestBuilder->setHttpHost($request['header']['host'] ?? '');
        $requestBuilder->setHttpUserAgent($request['header']['user-agent'] ?? '');
        $requestBuilder->setHttpAccept($request['header']['accept'] ?? '');
        $requestBuilder->setHttpAcceptLanguage($request['header']['accept-language'] ?? '');
        $requestBuilder->setHttpAcceptEncoding($request['header']['accept-encoding'] ?? '');
        $requestBuilder->setHttpConnection($request['header']['connection'] ?? '');
        $requestBuilder->setHttpUpgrade($request['header']['upgrade'] ?? '');
        $requestBuilder->setHttpOrigin($request['header']['origin'] ?? '');
        $requestBuilder->setHttpSecWebsocketVersion($request['header']['sec-websocket-version'] ?? '');
        $requestBuilder->setHttpSecWebsocketKey($request['header']['sec-websocket-key'] ?? '');
        $requestBuilder->setHttpSecWebsocketExtensions($request['header']['sec-websocket-extensions'] ?? '');
        $requestBuilder->setContentLength($request['header']['content-length'] ?? 0);
        $requestBuilder->setContentType('application/json');
        $requestBuilder->setClientId($request['header']['client-id'] ?? $request['cookie']['PHPSESSID'] ?? '');
        return $requestBuilder;
    }

    /**
     * run
     */
    public function run()
    {
        $this->server = new swoole_websocket_server("0.0.0.0", $this->options['p']);

        $this->server->set(array(
            'worker_num' => 4,
            'task_worker_num' => 10,
            'heartbeat_check_interval' => 30,
            'heartbeat_idle_time' => 600,
        ));

        $this->server->on("start", function () {
            echo "server start" . PHP_EOL;
        });

        $this->server->on("workerStart", function ($worker) {
            echo "worker start" . PHP_EOL;
        });

        $this->server->on('open', function ($server, $req) {
            echo "connection open: {$req->fd}\n";
            $this->clients[$req->fd] = get_object_vars($req);
        });

        $this->server->on('message', function ($server, $frame) {
            $request = $this->clients[$frame->fd];
            if (!$request) {
                return;
            }
            $request['rawData'] = $frame->data;
            $this->server->task($request, -1, function ($server, $task_id, Collector $responseCollector) use ($request) {
                if ($responseCollector !== false) {
                    $server->push($request['fd'], $responseCollector->response());
                }
            });
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $ResponseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::SWOOLE_WEB_SOCKET,
                $this->requestBuilder($server, $task_id, $from_id, $request)
            );
            $this->server->finish($ResponseCollector);
        });

        $this->server->on('finish', function ($server, $task_id, $data) {
            echo "AsyncTask Finish" . PHP_EOL;
        });

        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->start();
    }
}
