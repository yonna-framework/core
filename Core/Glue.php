<?php

namespace Yonna\Core;

use ReflectionClass;
use RuntimeException;

class Glue
{

    /**
     * 类粘合
     */
    protected static $glue = [];

    /**
     * 获取所有粘合体
     * @return array
     */
    private static function all(): array
    {
        return static::$glue ?? [];
    }

    /**
     * @param null $class
     * @return string | array
     */
    private static function fetch($class = null)
    {
        if (null === static::$glue) {
            static::$glue = Glue::all();
        }
        return $class === null ? static::$glue : (static::$glue[$class] ?? null);
    }

    /**
     * 粘合体关连
     * @param string $glue
     * @param string $class
     */
    public static function relating(string $glue, string $class): void
    {
        static::$glue[$glue] = $class;
    }

    /**
     * 粘合 * 工厂方法，创建实例，并完成依赖注入
     * @param string $class
     * @param array $params
     * @return object
     */
    public static function paste($class, $params = []): object
    {
        $g = static::fetch($class) ?? null;
        if (is_string($class) && $g) {
            return static::paste($g, $params);
        }

        // 如果不是反射类根据类名创建
        $class = is_string($class) ? new ReflectionClass($class) : $class;

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
     * Response dynamic, static calls to the object.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     *
     * @throws RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = Glue::paste(get_called_class(), $args);
        if (!$instance) {
            throw new RuntimeException('Glue root has not been set.');
        }
        return $instance->$method(...$args);
    }

}