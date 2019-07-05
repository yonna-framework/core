<?php

namespace Yonna\IO;

use Yonna\Foundation\System;

/**
 * Class Config
 * @package Yonna\Scope
 */
class Config
{

    private static $crypto_protocol = 'null';
    private static $crypto_type = '';
    private static $crypto_secret = '';
    private static $crypto_iv = '';

    /**
     * @param $key
     * @param null $default
     * @return array|bool|false|string|null
     */
    public static function env($key, $default = null)
    {
        return System::env($key, $default);
    }

    /**
     * @return string
     */
    public static function getCryptoProtocol(): string
    {
        return self::$crypto_protocol;
    }

    /**
     * @param string $crypto_protocol
     */
    public static function setCryptoProtocol(string $crypto_protocol): void
    {
        self::$crypto_protocol = $crypto_protocol;
    }

    /**
     * @return string
     */
    public static function getCryptoType(): string
    {
        return self::$crypto_type;
    }

    /**
     * @param string $crypto_type
     */
    public static function setCryptoType(string $crypto_type): void
    {
        self::$crypto_type = $crypto_type;
    }

    /**
     * @return string
     */
    public static function getCryptoSecret(): string
    {
        return self::$crypto_secret;
    }

    /**
     * @param string $crypto_secret
     */
    public static function setCryptoSecret(string $crypto_secret): void
    {
        self::$crypto_secret = $crypto_secret;
    }

    /**
     * @return string
     */
    public static function getCryptoIv(): string
    {
        return self::$crypto_iv;
    }

    /**
     * @param string $crypto_iv
     */
    public static function setCryptoIv(string $crypto_iv): void
    {
        self::$crypto_iv = $crypto_iv;
    }


}