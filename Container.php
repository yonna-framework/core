<?php
/**
 * Bootstrap Container
 */

namespace PhpureCore;

use PhpureCore\Container\Interfaces;
use ReflectionClass;
use ReflectionException;

class Container
{
    /**
     * 单例
     */
    protected static $instance;

    /**
     * 容器所管理的实例
     */
    protected static $instances = [];

    /**
     * 接点
     */
    protected static $interfaces = null;

    public function __construct()
    {

    }

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
     * @param null $class
     * @return string | array
     */
    public static function getInterfaces($class = null)
    {
        if (null === static::$interfaces) {
            static::$interfaces = Interfaces::get();
        }
        return $class === null ? static::$interfaces : (static::$interfaces[$class] ?? null);
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
            static::$instances[$class] = static::make($class, $params);
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
        return static::make($class, $params);
    }

    /**
     * 工厂方法，创建实例，并完成依赖注入
     * @param string $class
     * @param array $params
     * @return object
     */
    protected static function make($class, $params = []): object
    {
        // 别称
        $alia = static::getInterfaces($class) ?? null;
        if (is_string($class) && $alia) {
            return static::make($alia, $params);
        }

        // 如果不是反射类根据类名创建
        try {
            $class = is_string($class) ? new ReflectionClass($class) : $class;
        } catch (ReflectionException $e) {
            dd($e);
        }

        // 如果传的入参不为空，则根据入参创建实例
        if (!empty($params)) {
            return $class->newInstanceArgs($params);
        }

        // 获取构造方法
        $constructor = $class->getConstructor();

        // 获取构造方法参数
        $parameterClasses = $constructor ? $constructor->getParameters() : [];

        if (empty($parameterClasses)) {
            // 如果构造方法没有入参，直接创建
            return $class->newInstance();
        } else {
            // 如果构造方法有入参，迭代并递归创建依赖类实例
            foreach ($parameterClasses as $parameterClass) {
                $paramClass = $parameterClass->getClass();
                $params[] = static::make($paramClass);
            }
            // 最后根据创建的参数创建实例，完成依赖的注入
            return $class->newInstanceArgs($params);
        }
    }

}