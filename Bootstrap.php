<?php
/**
 * Bootstrap core
 */

namespace PhpureCore;

use Exception;
use PhpureCore\Bootstrap\Config;
use PhpureCore\Bootstrap\Env;
use PhpureCore\Bootstrap\Functions;
use PhpureCore\Interfaces\Cargo;
use PhpureCore\Interfaces\Request;
use PhpureCore\Mapping\BootType;

class Bootstrap implements Interfaces\Bootstrap
{


    /**
     * Bootstrap constructor.
     * @param $root
     * @param null $env_name
     * @param null $boot_type
     */
    public function __construct($root, $env_name = null, $boot_type = null)
    {
        /**
         * @var $Cargo \PhpureCore\Bootstrap\Cargo
         */
        $Cargo = Container::singleton(Cargo::class, [
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
        IO::response(Container::singleton(Request::class, $Cargo));
        return $this;
    }

}