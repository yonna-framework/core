<?php
/**
 * Bootstrap core
 */

namespace Yonna\Bootstrap;

use ErrorException;
use Throwable;
use Yonna\Core;
use Yonna\IO\RequestBuilder;
use Yonna\Throwable\Exception;
use Yonna\IO\IO;
use Yonna\IO\Request;
use Yonna\Log\FileLog;
use Yonna\Response\Collector;
use Yonna\Response\Response;

class Bootstrap
{

    public function __construct()
    {
        return $this;
    }

    /**
     * Bootstrap constructor.
     * @param $root
     * @param null $env_name
     * @param null $boot_type
     * @param RequestBuilder|null $builder
     * @return Collector
     * @throws null
     */
    public function boot($root, $env_name, $boot_type, RequestBuilder $builder = null)
    {
        /**
         * @var $Cargo Cargo
         */
        $Cargo = Core::get(Cargo::class, [
            'root' => $root ?? __DIR__ . '/../../../../',
            'env_name' => $env_name ?? 'example',
            'boot_type' => $boot_type ?? BootType::AJAX_HTTP,
        ]);

        try {

            /**
             * Cargo
             */

            // 环境
            $Cargo = Env::install($Cargo);
            // 基础库
            $Cargo = Foundation::install($Cargo);
            // 配置
            $Cargo = Config::install($Cargo);
            // 自定义函数
            $Cargo = Functions::install($Cargo);

            /**
             * @var Request $request
             */
            $request = Core::get(Request::class, $Cargo, $builder);

            /**
             * @var IO $io
             */
            $io = Core::singleton(IO::class);
            $collector = $io->response($request);

        } catch (Throwable $e) {
            // log
            $log = Core::get(FileLog::class);
            $log->throwable($e);
            // response
            $origin = true;
            if (!(getenv('IS_DEBUG') || getenv('IS_DEBUG') !== 'true')) {
                $origin = false;
            }
            $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if (!empty($request)) {
                $requestMethod = $request->getRequestMethod();
                $userAgent = $request->getHttpUserAgent();
            }
            if ($requestMethod !== 'GET') {
                $origin = false;
            } else if ($userAgent !== null && strpos(strtolower($userAgent), 'postman') !== false) {
                $origin = false;
            }
            if ($origin === true) {
                Exception::origin($e);
                exit();
            } else {
                if ($e instanceof Exception\PermissionException) {
                    $collector = Response::notPermission($e->getMessage());
                } else if ($e instanceof ErrorException) {
                    $collector = Response::error($e);
                } else {
                    $collector = Response::throwable($e);
                }
            }
        }
        return $collector;
    }

}