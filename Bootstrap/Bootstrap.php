<?php
/**
 * Bootstrap core
 */

namespace Yonna\Bootstrap;

use Yonna\Core;
use Yonna\Exception\Exception;
use Yonna\IO\IO;
use Yonna\IO\Request;
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
     * @param null $extend
     * @return Collector
     */
    public function boot($root, $env_name, $boot_type, $extend = null)
    {
        /**
         * @var $Cargo Cargo
         */
        $Cargo = Core::get(Cargo::class, [
            'root' => $root ?? __DIR__ . '/../../../../',
            'env_name' => $env_name ?? 'example',
            'boot_type' => $boot_type ?? BootType::AJAX_HTTP,
        ]);
        // extend
        $Cargo->extend = $extend;
        try {

            // 环境
            $Cargo = Env::install($Cargo);
            // 基础库
            $Cargo = Foundation::install($Cargo);
            // 配置
            $Cargo = Config::install($Cargo);
            // 自定义函数
            $Cargo = Functions::install($Cargo);

        } catch (\Exception $e) {
            if ((getenv('IS_DEBUG') && getenv('IS_DEBUG') === 'true')) {
                Exception::abort($e->getMessage());
            } else {
                return Response::abort($e->getMessage());
            }
        }

        /**
         * @var Request $request
         */
        $request = Core::singleton(Request::class, $Cargo);
        /**
         * @var IO $io
         */
        $io = Core::singleton(IO::class);
        return $io->response($request);
    }

}