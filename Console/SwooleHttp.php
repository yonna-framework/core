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
    private $params = null;

    /**
     * SwooleHttp constructor.
     * @throws Exception
     */
    public function __construct()
    {
        if (!function_exists('swoole_http_server')) {
            throw new Exception('function swoole_http_server not exists');
        }
        $this->params = $this->getParams(['port']);
        return $this;
    }

    public function run($root_path)
    {
        $this->server = new swoole_http_server("0.0.0.0", $this->params['port']);

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
            if (!$request->post['post']) {
                $request->post = $request->rawContent();
            } else {
                $request->post = $request->post['post'];
            }
            $request = get_object_vars($request);
            $this->server->task($request, -1, function ($server, $task_id, $result) use ($response) {
                if ($result !== false) {
                    $response->end($result);
                    return;
                } else {
                    $response->status(404);
                    $response->end();
                }
            });
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) use ($root_path) {
            Core::bootstrap(
                realpath($root_path),
                'example',
                BootType::SWOOLE_HTTP
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