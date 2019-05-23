<?php
/**
 * input / output
 */

namespace PhpureCore\IO;

use Arr;
use Closure;
use Str;
use PhpureCore\Glue\Handle;

class IO
{

    /**
     * @var \PhpureCore\IO\Request $request
     */
    private $request = null;

    public function __construct()
    {
        return $this;
    }

    public function response(object $request)
    {
        $this->request = $request;
        $data = $this->request->input->getData();
        $scope = $data['scope'] ?? null;
        if (!$scope) {
            Handle::abort('no scope');
        }
        $scope = Str::upper($scope);
        $scope = Arr::get($this->request->cargo->config, "scope.{$request->method}.{$scope}");
        $necks = $scope['neck'];
        $call = $scope['call'];
        $tails = $scope['tail'];
        if ($call instanceof Closure) {
            foreach ($necks as $neck) $neck($request);
            $response = $call($request);
            foreach ($tails as $tail) $tail($response);
            var_dump($response);
        }
        Handle::abort('io destroy');
    }

}