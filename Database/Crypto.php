<?php

namespace PhpureCore\Database;

use PhpureCore\Config\Crypto as ConfigCrypto;
use PhpureCore\Glue\Response;

class Crypto
{

    private static $crypto_type = null;
    private static $crypto_secret = null;
    private static $crypto_iv = null;

    public function __construct($crypto_type, $crypto_secret, $crypto_iv)
    {
        static::$crypto_type = $crypto_type;
        static::$crypto_secret = $crypto_secret;
        static::$crypto_iv = $crypto_iv;
    }

    /**
     * @param $str
     * @return string
     */
    public static function encrypt(string $str)
    {
        if (!static::$crypto_type || !static::$crypto_secret || !static::$crypto_iv) {
            Response::abort('Crypto encrypt error');
        }
        return openssl_encrypt($str, static::$crypto_type, static::$crypto_secret, 0, static::$crypto_iv);
    }

    /**
     * @param $str
     * @return string
     */
    public static function decrypt(string $str)
    {
        if (!static::$crypto_type || !static::$crypto_secret || !static::$crypto_iv) {
            Response::abort('Crypto decrypt error');
        }
        return openssl_decrypt($str, static::$crypto_type, static::$crypto_secret, 0, static::$crypto_iv);
    }

}