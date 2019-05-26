<?php

namespace PhpureCore\Database;

use PhpureCore\Config\Crypto as ConfigCrypto;
use PhpureCore\Glue\Response;

class Crypto
{

    /**
     * @param $str
     * @return string
     */
    public static function encrypt(string $str)
    {
        $type = ConfigCrypto::get('db_type');
        $secret = ConfigCrypto::get('db_secret');
        $iv = ConfigCrypto::get('db_iv');
        if (!$type || !$secret || !$iv) {
            Response::abort('Crypto encrypt error');
        }
        return openssl_encrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * @param $str
     * @return string
     */
    public static function decrypt(string $str)
    {
        $type = ConfigCrypto::get('db_type');
        $secret = ConfigCrypto::get('db_secret');
        $iv = ConfigCrypto::get('db_iv');
        if (!$type || !$secret || !$iv) {
            Response::abort('Crypto encrypt error');
        }
        return openssl_decrypt($str, $type, $secret, 0, $iv);
    }

}