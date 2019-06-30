<?php

namespace Yonna\Config;

use Yonna\Exception\Exception;
use Yonna\Glue\Response;

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
        if (empty($key)) Exception::throw('no key');
        if (empty($value)) Exception::throw('no value');
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