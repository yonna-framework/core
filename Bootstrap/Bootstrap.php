<?php
/**
 * Bootstrap core
 */

namespace PhpureCore\Bootstrap;

use Exception;
use PhpureCore\Container;
use PhpureCore\Mapping\BootType;
use PhpureCore\IO\IO;
use PhpureCore\Interfaces\Request;

class Bootstrap implements \PhpureCore\Interfaces\Bootstrap
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
         * @var $Cargo Cargo
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
            $Cargo = Foundation::install($Cargo);
            // 配置
            $Cargo = Config::install($Cargo);

        } catch (Exception $e) {
            dd($e);
        }
        IO::response(Container::singleton(Request::class, $Cargo));
        return $this;
    }

}