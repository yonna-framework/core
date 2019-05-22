<?php

namespace PhpureCore\Config;

use PhpureCore\Glue\Handle;

class Crypto extends Arrow
{

    const name = 'crypto';

    /**
     * 设置密钥
     * @param string $key
     * @param string $value
     */
    public static function set(string $key, string $value)
    {
        if (empty($key)) Handle::exception('no key');
        if (empty($value)) Handle::exception('no value');
        $key = strtoupper($key);
        self::$stack[self::name][$key] = $value;
    }

    /**
     * 获取密钥
     * @param string $key
     * @return string
     */
    public static function get(string $key): string
    {
        $key = strtoupper($key);
        return self::$stack[self::name][$key] ?? '';
    }

}