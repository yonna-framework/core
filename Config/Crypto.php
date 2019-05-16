<?php

namespace PhpureCore\Config;

use Closure;
use Exception;

class Crypto extends Arrow
{

    const name = 'crypto';

    public function __construct()
    {
        if (!isset(self::$stack[self::name])) {
            self::$stack[self::name] = array();
        }
        return $this;
    }

    /**
     * 设置密钥
     * @param string $key
     * @param string $value
     */
    public static function set(string $key, string $value)
    {
        if (empty($key)) throw new Exception('must key');
        if (empty($value)) throw new Exception('must value');
        self::$stack[self::name][$key] = $value;
    }

    /**
     * 获取密钥
     * @param string $key
     * @return string
     */
    public static function get(string $key): string
    {
        return self::$stack[self::name][$key] ?? '';
    }

}