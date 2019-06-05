<?php

namespace PhpureCore\Console;

use Exception;


/**
 * Class Main
 * @package PhpureCore\Console
 */
class SwooleTcp extends Console
{

    private $server = null;
    private $params = null;


    /**
     * SwooleHttp constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if (!function_exists('swoole_server')) {
            throw new Exception('function swoole_server not exists');
        }
        $this->params = $this->getParams(['port']);
        return $this;
    }

    public function run($root_path)
    {
        $this->server = new swoole_server("0.0.0.0", $this->params['port']);

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

        $this->server->on('connect', function ($server, $fd) {
            echo "connection open: {$fd}\n";
        });
        $this->server->on('receive', function ($server, $fd, $reactor_id, $data) {
            $request = $data;
            /**
             * 处理数据
             */
            $this->server->task($request, -1, function ($server, $task_id, $result) use ($fd) {
                if ($result !== false) {
                    $server->send($fd, $result);
                    return;
                }
            });
        });
        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $data = $this->io($request);
            $this->server->finish($data);
        });

        $this->server->on('finish', function ($server, $data) {
            echo "AsyncTask Finish" . PHP_EOL;
        });

        $this->server->on('close', function ($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $this->server->start();
    }
}
