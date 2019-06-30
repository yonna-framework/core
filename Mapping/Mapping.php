<?php

namespace Yonna\Mapping;

use Exception;
use ReflectionClass;
use ReflectionException;

abstract class Mapping
{

    protected static $map_data = [];
    protected static $fetch_cache = [];

    /**
     * 获取反射类名
     * @return string
     */
    private static function getClassName()
    {
        return get_called_class() ?? __CLASS__;
    }

    /**
     * 设置一个值的自定义参数
     * @param $value
     * @param $optKey
     * @param $optVal
     */
    private static function setOptions($value, $optKey, $optVal)
    {
        $className = static::getClassName();
        if (!isset(static::$map_data[$className])) {
            static::$map_data[$className] = [];
        }
        static::$map_data[$className][$value][$optKey] = $optVal;
    }

    /**
     * 获取一个值的自定义参数
     * @param $value
     * @param $optKey
     * @return mixed
     */
    public static function getOption($value, $optKey)
    {
        $self = new static();
        $map_data = $self::$map_data;
        return isset($map_data[$self::getClassName()][$value][$optKey])
            ? $map_data[$self::getClassName()][$value][$optKey] : null;
    }

    /**
     * 设置一个值的label
     * @param $value
     * @param $label
     */
    protected static function setLabel($value, $label)
    {
        static::setOptions($value, 'label', $label);
    }

    /**
     * 获取一个值的label
     * @param $value
     * @return string
     */
    public static function getLabel($value)
    {
        return static::getOption($value, 'label') ?: '';
    }

    /**
     * 设置一个值的status
     * @param $value
     * @param $status
     */
    protected static function setStatus($value, $status)
    {
        static::setOptions($value, 'status', $status);
    }

    /**
     * 设置一个值的status
     * @param $value
     * @return string
     */
    public static function getStatus($value)
    {
        return static::getOption($value, 'status') ?: '1';
    }

    /**
     * 反射mapping的数据
     * @return mixed
     * @throws Exception
     */
    public static function fetch()
    {
        $class = static::getClassName();
        if (!isset(static::$fetch_cache[$class])) {
            try {
                $objClass = new ReflectionClass($class);
                $arrConst = $objClass->getConstants();
                static::$fetch_cache[$class] = $arrConst;
            } catch (ReflectionException $e) {
                throw new Exception($e->getMessage());
            }
        }
        return static::$fetch_cache[$class] ?? [];
    }

    public static function toArray()
    {
        $arr = static::fetch();
        sort($arr);
        return $arr;
    }

    public static function toJson()
    {
        return json_encode(static::fetch() ?: []);
    }

    public static function toKV($target = 'label')
    {
        $data = static::fetch();
        $kv = [];
        foreach ($data as $v) {
            $kv[$v] = static::getOption($v, $target) ?: null;
        }
        return $kv;
    }

    public static function toMixed()
    {
        $data = static::fetch();
        $kv = [];
        $className = static::getClassName();
        foreach ($data as $v) {
            $kv[$v] = static::$map_data[$className][$v];
        }
        return $kv;
    }

}