<?php

namespace PhpureCore\Console;

use Exception;
use PhpureCore\Core;
use PhpureCore\Mapping\BootType;

/**
 * swoole http
 * Class SwooleHttp
 */
class SwooleHttp extends Console
{

    private $server = null;
    private $root_path = null;
    private $options = null;

    /**
     * SwooleHttp constructor.
     * @param $root_path
     * @param $options
     * @throws Exception
     */
    public function __construct($root_path, $options)
    {
        if (!class_exists('swoole_http_server')) {
            throw new Exception('class swoole_http_server not exists');
        }
        $this->root_path = $root_path;
        $this->options = $options;
        $this->checkParams($this->options, ['p']);
        return $this;
    }

    public function run()
    {
        $this->server = new \swoole_http_server("0.0.0.0", $this->options['p']);

        $this->server->set(array(
            'worker_num' => 4,
            'task_worker_num' => 10,
        ));

        $this->server->on("start", function () {
            echo "server start" . PHP_EOL;
        });

        $this->server->on("workerStart", function ($worker) {
            echo "worker start" . PHP_EOL;
        });

        $this->server->on("request", function ($request, $response) {
            $GLOBALS['swh'][$request->fd] = array(
                'request' => $request,
                'response' => $response
            );
            $this->server->task(get_object_vars($request), -1, function ($server, $task_id, $result) use ($response) {
                if ($result !== false) {
                    $response->end($result);
                    return;
                } else {
                    $response->status(404);
                    $response->end();
                }
            });
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $resp = $this->getResponse();
            print_r($resp);
            Core::bootstrap(
                realpath($this->root_path),
                'example',
                BootType::SWOOLE_HTTP,
                array(
                    'fd' => $request['fd'],
                    'server' => $server,
                    'task_id' => $task_id,
                    'from_id' => $from_id,
                    'request' => $request,
                )
            );
            $data = $this->io($request);
            $this->server->finish($data);
        });

        $this->server->on('finish', function ($server, $task_id, $data) {
            echo "AsyncTask Finish" . PHP_EOL;
        });

        $this->server->start();
    }
}