<?php

namespace PhpureCore\Console;

use Exception;

/**
 * swoole http
 * Class SwooleHttp
 */
class SwooleWebsocket extends Console
{

    private $server = null;
    private $params = null;
    private $clients = array();

    /**
     * SwooleHttp constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if (!function_exists('swoole_websocket_server')) {
            throw new Exception('function swoole_websocket_server not exists');
        }
        $this->params = $this->getParams(['port']);
        return $this;
    }

    public function run($root_path)
    {
        $this->server = new swoole_websocket_server("0.0.0.0", $this->params['port']);

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
            if (!$request) return;
            $request['post'] = $frame->data;
            $this->server->task($request, -1, function ($server, $task_id, $result) use ($request) {
                if ($result !== false) {
                    $server->push($request['fd'], $result);
                    return;
                }
            });
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $data = $this->io($request);
            $this->server->finish($data);
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
