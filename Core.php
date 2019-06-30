<?php

/**
 * Core
 */

namespace Yonna;

use Yonna\Core\Glue;
use Yonna\Glue\Bootstrap;

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
     * @param null $extend
     * @return IO\ResponseCollector
     */
    public static function bootstrap($root, $env_name, $boot_type, $extend = null)
    {
        // autoload
        spl_autoload_register(function ($res) use ($root) {
            $res = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $res);
            foreach ([$root] as $path) {
                $file = $path . DIRECTORY_SEPARATOR . $res . '.php';
                if (is_file($file)) {
                    require($file);
                    break;
                }
            }
        });
        // default glues relation
        Glue::relating(\Yonna\Glue\Bootstrap::class, \Yonna\Bootstrap\Bootstrap::class);
        Glue::relating(\Yonna\Glue\Response::class, \Yonna\IO\Response::class);
        Glue::relating(\Yonna\Glue\ResponseCollector::class, \Yonna\IO\ResponseCollector::class);
        Glue::relating(\Yonna\Glue\Cargo::class, \Yonna\Bootstrap\Cargo::class);
        Glue::relating(\Yonna\Glue\IO::class, \Yonna\IO\IO::class);
        Glue::relating(\Yonna\Glue\Request::class, \Yonna\IO\Request::class);
        Glue::relating(\Yonna\Glue\Scope::class, \Yonna\Config\Scope::class);
        // boot
        return Bootstrap::boot($root, $env_name, $boot_type, $extend);
    }

}