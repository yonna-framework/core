<?php

namespace Yonna\Console;

use Exception;
use Yonna\Core;
use Yonna\IO\ResponseCollector;
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

    public function run()
    {
        $this->server = new swoole_websocket_server("0.0.0.0", $this->options['p']);

        $this->server->set(array(
            'worker_num' => 4,
            'task_worker_num' => 10,
            'heartbeat_check_interval' => 10,
            'heartbeat_idle_time' => 180,
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
            $requestVars['rawData'] = $frame->data;
            $this->server->task($requestVars, -1, function ($server, $task_id, ResponseCollector $responseCollector) use ($request) {
                if ($responseCollector !== false) {
                    $server->push($request['fd'], $responseCollector->toJson());
                }
            });
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $ResponseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::SWOOLE_WEB_SOCKET,
                array(
                    'connections' => $server->connections,
                    'clients' => $server->clients,
                    'task_id' => $task_id,
                    'from_id' => $from_id,
                    'request' => $request,
                )
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
