<?php

namespace PhpureCore\IO;

use PhpureCore\Handle;

class Crypto
{

    /**
     * @param $str
     * @return string
     */
    private static function encrypt($str)
    {
        $type = getenv('CRYPTO_IO_REQUEST_TYPE') ?? null;
        $secret = getenv('CRYPTO_IO_REQUEST_SECRET') ?? null;
        $iv = getenv('CRYPTO_IO_REQUEST_IV') ?? null;
        if (!$type || !$secret || !$iv) {
            return 'Crypto encrypt error';
        }
        return openssl_encrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * @param $str
     * @return string
     */
    private static function decrypt($str)
    {
        $type = getenv('CRYPTO_IO_REQUEST_TYPE') ?? null;
        $secret = getenv('CRYPTO_IO_REQUEST_SECRET') ?? null;
        $iv = getenv('CRYPTO_IO_REQUEST_IV') ?? null;
        if (!$type || !$secret || !$iv) {
            return 'Crypto decrypt error';
        }
        return openssl_decrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * 展示所有的 Cipher Methods
     */
    public static function cipherMethods()
    {
        Handle::abort('Cipher Methods', openssl_get_cipher_methods());
    }

    /**
     * 获得加密的自定义协议头
     * 当body数据以此为协议头时，认为其为加密串
     */
    public static function protocol()
    {
        return getenv('CRYPTO_IO_REQUEST_PROTOCOL') ?? 'CRYPTO|';
    }

    /**
     * 是否隐秘请求
     * @param Request $request
     * @return bool
     */
    public static function isCrypto(Request $request)
    {
        return strpos($request->body, self::protocol()) === 0;
    }

    /**
     * 处理input
     * @param Request $request
     * @return bool
     */
    public static function input(Request $request)
    {
        return self::decrypt(str_replace_once(self::protocol(), '', $request->body));
    }

    /**
     * 是否隐秘请求
     * @param Request $request
     * @return bool
     */
    public static function response(Request $request)
    {
        return strpos($request->body, self::protocol()) === 0;
    }

}