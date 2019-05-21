<?php
/**
 * Core
 */

namespace PhpureCore;

use PhpureCore\Core\Glue;

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
     * @return Container
     */
    public static function getInstance(): Container
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
     * bootstarp
     * @param $root
     * @param null $env_name
     * @param null $boot_type
     */
    public static function bootstarp($root, $env_name = null, $boot_type = null)
    {
        Glue::d41d8cd98f00b204e9800998ecf8427e();
        \Bootstrap::boot($root, $env_name, $boot_type);
    }

}