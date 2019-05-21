<?php
/**
 * Bootstrap core
 */

namespace PhpureCore\Bootstrap;

use Exception;
use PhpureCore\Core;
use PhpureCore\Glue\Cargo;
use PhpureCore\Glue\IO;
use PhpureCore\Glue\Request;
use PhpureCore\Mapping\BootType;

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
     * @return Bootstrap
     */
    public function boot($root, $env_name = null, $boot_type = null)
    {
        /**
         * @var $Cargo \PhpureCore\Bootstrap\Cargo
         */
        $Cargo = Core::singleton(Cargo::class, [
            'root' => $root ?? '',
            'env_name' => $env_name ?? 'example',
            'boot_type' => $boot_type ?? BootType::AJAX_HTTP,
        ]);
        try {
            // 环境
            $Cargo = Env::install($Cargo);
            // 基础功能
            $Cargo = Functions::install($Cargo);
            // 配置
            $Cargo = Config::install($Cargo);

        } catch (Exception $e) {
            dd($e);
        }
        IO::response(Core::singleton(Request::class, $Cargo));
        return $this;
    }

}