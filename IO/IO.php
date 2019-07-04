<?php
/**
 * input / output
 */

namespace Yonna\IO;

use Closure;
use Yonna\Foundation\Arr;
use Yonna\Foundation\Str;
use Yonna\Response\Collector;
use Yonna\Response\Response;
use Yonna\Scope\Config;
use Yonna\Scope\Scope;

class IO
{

    public function __construct()
    {
        return $this;
    }

    /**
     * @param Request $request
     * @return Collector
     */
    public function response(Request $request)
    {
        $input = json_decode($request->getInput(), true);
        $scope = $input['scope'] ?? null;
        if (!$scope) {
            return Response::abort('no scope');
        }
        dd(Config::fetch());
        $scope = Str::upper($scope);
        $scope = Arr::get(Config::fetch(), "scope.{$request->method}.{$scope}");
        if (!$scope) {
            return Response::abort('no scoped');
        }
        if ($scope['call'] instanceof Closure) {
            if ($scope['before']) {
                foreach ($scope['before'] as $before) {
                    $before($request);
                }
            }
            $response = $scope['call']($request);
            if ($scope['after']) {
                foreach ($scope['after'] as $after) {
                    $after($scope['after'], $response);
                }
            }
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
            if (!($response instanceof Collector)) {
                $response = Response::exception('Response must instanceof ResponseCollector');
            }
            return $response;
        }
        return Response::abort('io destroy');
    }

}