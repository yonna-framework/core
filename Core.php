<?php
/**
 * Core
 */

namespace PhpureCore;

use PhpureCore\Core\Glue;
use PhpureCore\Glue\Bootstrap;
use PhpureCore\Glue\Handle;

require __DIR__ . '/Core/include.php';

class Core
{
    /**
     * 单例
     */
    protected static $instance;

    /**
     * 核心所管理的实例
     */
    protected static $instances = [];

    /**
     * @return Core
     */
    public static function getInstance(): Core
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @return array
     */
    public static function getInstances(): array
    {
        return static::$instances;
    }

    /**
     * 获取实例
     * @param string $class
     * @param array $params
     * @return object
     */
    public static function singleton($class, ...$params): object
    {
        if (isset(static::$instances[$class])) {
            return static::$instances[$class];
        } else {
            static::$instances[$class] = Glue::paste($class, $params);
        }

        return static::$instances[$class];
    }

    /**
     * 获取新实例
     * @param string $class
     * @param array $params
     * @return object
     */
    public static function get($class, ...$params): object
    {
        return Glue::paste($class, $params);
    }

    /**
     * 驱动净神
     * bootstrap
     * @param $root
     * @param null $env_name
     * @param null $boot_type
     */
    public static function bootstrap($root, $env_name = null, $boot_type = null)
    {
        // default glues
        Glue::link(\PhpureCore\Glue\Bootstrap::class, \PhpureCore\Bootstrap\Bootstrap::class);
        Glue::link(\PhpureCore\Glue\Cargo::class, \PhpureCore\Bootstrap\Cargo::class);
        Glue::link(\PhpureCore\Glue\IO::class, \PhpureCore\IO\IO::class);
        Glue::link(\PhpureCore\Glue\Request::class, \PhpureCore\IO\Request::class);
        Glue::link(\PhpureCore\Glue\Crypto::class, \PhpureCore\IO\Crypto::class);
        Glue::link(\PhpureCore\Glue\Handle::class, \PhpureCore\Core\Handle::class);
        Glue::link(\PhpureCore\Glue\HandleCollector::class, \PhpureCore\Core\HandleCollector::class);
        // boot
        Bootstrap::boot($root, $env_name, $boot_type);
    }

}