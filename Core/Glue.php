<?php

namespace PhpureCore\Core;

use ReflectionClass;
use ReflectionException;
use RuntimeException;

class Glue
{
    const default = [
        \Bootstrap::class => \PhpureCore\Bootstrap\Bootstrap::class,
        \Cargo::class => \PhpureCore\Bootstrap\Cargo::class,
        \IO::class => \PhpureCore\IO\IO::class,
        \Request::class => \PhpureCore\IO\Request::class,
    ];

    /**
     * 类粘合
     */
    protected static $glue = null;


    /**
     * @return array
     */
    public static function all(): array
    {
        if (null === static::$glue) {
            $default = self::default;

            static::$glue = array_merge($default);
        }
        return static::$glue;
    }

    /**
     * @param null $class
     * @return string | array
     */
    public static function get($class = null)
    {
        if (null === static::$glue) {
            static::$glue = Glue::all();
        }
        return $class === null ? static::$glue : (static::$glue[$class] ?? null);
    }

    /**
     * 工厂方法，创建实例，并完成依赖注入
     * @param string $class
     * @param array $params
     * @return object
     */
    public static function paste($class, $params = []): object
    {
        // 粘合
        $g = static::get($class) ?? null;
        if (is_string($class) && $g) {
            return static::paste($g, $params);
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
                $params[] = static::paste($paramClass);
            }
            // 最后根据创建的参数创建实例，完成依赖的注入
            return $class->newInstanceArgs($params);
        }
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::paste(get_called_class(), $args);
        if (!$instance) {
            throw new RuntimeException('A glue root has not been set.');
        }
        return $instance->$method(...$args);
    }

    public static function d41d8cd98f00b204e9800998ecf8427e(): void
    {
        foreach (array_keys(static::get()) as $gk) {
            $path = realpath(__DIR__ . '/../Glue/' . $gk . '.php');
            require($path);
        }
    }

}