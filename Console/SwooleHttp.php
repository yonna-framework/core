<?php

namespace Yonna\Console;

use Exception;
use Yonna\Core;
use Yonna\IO\ResponseCollector;
use Yonna\Mapping\BootType;
use Swoole\Http\Response;
use swoole_http_server;

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
        $this->checkParams($this->options, ['p', 'e']);
        return $this;
    }

    public function run()
    {
        $this->server = new swoole_http_server("0.0.0.0", $this->options['p']);

        $this->server->set(array(
            'worker_num' => 4,
            'task_worker_num' => 10,
            'http_compression' => true,
        ));

        $this->server->on("start", function () {
            echo "server start" . PHP_EOL;
        });

        $this->server->on("workerStart", function ($worker) {
            echo "worker start" . PHP_EOL;
        });

        $this->server->on("request", function ($request, Response $response) {
            $request->rawData = $request->rawContent();
            $requestVars = get_object_vars($request);
            $requestVars['rawData'] = $request->rawContent();
            $this->server->task($requestVars, -1, function ($server, $task_id, ResponseCollector $responseCollector) use ($response) {
                $response->header('Server', 'Pure');
                if ($responseCollector !== false) {
                    $responseHeader = $responseCollector->getHeader('arr');
                    foreach ($responseHeader as $hk => $hv) {
                        $response->header($hk, $hv);
                    }
                    $response->status(200);
                    $response->end($responseCollector->toJson());
                } else {
                    $response->status(403);
                    $response->end();
                }
            });
        });

        $this->server->on('task', function ($server, $task_id, $from_id, $request) {
            $ResponseCollector = Core::bootstrap(
                realpath($this->root_path),
                $this->options['e'],
                BootType::SWOOLE_HTTP,
                array(
                    'connections' => $server->connections,
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

        $this->server->start();
    }
}