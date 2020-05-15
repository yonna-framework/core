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
        $input = $request->getInput() ?? [];
        $scope = $input['scope'] ?? null;
        if (!$scope) {
            return Response::abort('no scope');
        }
        $scopeStr = Str::upper($scope);
        $scope = Arr::get(Config::fetch(), "{$request->getRequestMethod()}.{$scopeStr}");
        if (!$scope) {
            return Response::abort('no scope isset');
        }
        if ($scope['call'] instanceof Closure) {
            $request->setScope($scopeStr);
            if ($scope['before']) {
                foreach ($scope['before'] as $before) {
                    $request = $before($request);
                }
            }
            $response = $scope['call']($request);
            if ($scope['after']) {
                foreach ($scope['after'] as $after) {
                    $response = $after($request, $response);
                }
            }
            // response
            if ($response === null) {
                $response = Response::notFound('You should return some response but not null');
            } else if (is_array($response)) {
                $response = Response::success('fetch array success', $response);
            } else if (is_string($response)) {
                $response = Response::success('fetch string success', ['string' => $response]);
            } else if (is_numeric($response)) {
                $response = Response::success('fetch number success', ['number' => $response]);
            } else if (is_bool($response)) {
                $response = $response === true ? Response::success('fetch boolean success', ['bool' => $response]) : Response::error('error');
            }
            if (!($response instanceof Collector)) {
                $response = Response::notFound('Response must instanceof ResponseCollector');
            }
            return Crypto::output($request, $response);
        }
        return Response::abort('io fail');
    }

}