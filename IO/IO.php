<?php
/**
 * input / output
 */

namespace PhpureCore\IO;

use Arr;
use Closure;
use Exception;
use Str;
use PhpureCore\Glue\Response;
use PhpureCore\Mapping\BootType;

class IO
{

    /**
     * @var Request $request
     */
    private $request = null;

    public function __construct()
    {
        return $this;
    }

    /**
     * @param \PhpureCore\IO\ResponseCollector $response
     */
    private function end($response)
    {
        switch ($this->request->cargo->boot_type) {
            case BootType::AJAX_HTTP:
                $response->end();
                break;
        }
    }

    public function response(object $request)
    {
        try {
            $request->init();
        } catch (Exception $e) {
            Response::notPermission($e->getMessage())->end();
        }
        $this->request = $request;
        $data = $this->request->input->getData();
        $scope = $data['scope'] ?? null;
        if (!$scope) {
            Response::abort('no scope')->end();
        }
        $scope = Str::upper($scope);
        $scope = Arr::get($this->request->cargo->config, "scope.{$request->method}.{$scope}");
        if (!$scope) {
            Response::abort('no scoped')->end();
        }
        $necks = $scope['neck'];
        $call = $scope['call'];
        $tails = $scope['tail'];
        if ($call instanceof Closure) {
            if ($necks) foreach ($necks as $neck) $neck($request);
            $response = $call($request);
            if ($tails) foreach ($tails as $tail) $tail($request, $response);
            // response
            if (is_array($response)) {
                $response = Response::success('fetch array success', $response);
            } else if (is_string($response)) {
                $response = Response::success($response, ['string' => $response]);
            } else if (is_numeric($response)) {
                $response = Response::success('fetch number success', [$response]);
            } else if (is_bool($response)) {
                $response ? $response = Response::success('success bool') : Response::error('error bool');
            }
            if (!($response instanceof ResponseCollector)) {
                $response = Response::exception('Response must instanceof ResponseCollector');
            }
            $response->end();
        }
        Response::abort('io destroy')->end();
    }

}