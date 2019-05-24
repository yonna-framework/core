<?php

namespace PhpureCore\Database;

use PhpureCore\Config\Crypto as ConfigCrypto;
use PhpureCore\Glue\Response;
use Str;

class Crypto
{

    /**
     * @param $str
     * @return string
     */
    private static function encrypt(string $str)
    {
        $type = ConfigCrypto::get('io_request_type');
        $secret = ConfigCrypto::get('io_request_secret');
        $iv = ConfigCrypto::get('io_request_iv');
        if (!$type || !$secret || !$iv) {
            Response::abort('Crypto encrypt error');
        }
        return openssl_encrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * @param $str
     * @return string
     */
    private static function decrypt(string $str)
    {
        $type = ConfigCrypto::get('io_request_type');
        $secret = ConfigCrypto::get('io_request_secret');
        $iv = ConfigCrypto::get('io_request_iv');
        if (!$type || !$secret || !$iv) {
            Response::abort('Crypto encrypt error');
        }
        return openssl_decrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * 获得加密的自定义协议头
     * 当body数据以此为协议头时，认为其为加密串
     */
    public static function protocol(): string
    {
        return ConfigCrypto::get('io_request_protocol') ?? 'CRYPTO|';
    }

    /**
     * 是否隐秘请求
     * @param Request $request
     * @return bool
     */
    public static function isCrypto(Request $request): bool
    {
        return strpos($request->body, self::protocol()) === 0;
    }

    /**
     * 对照 IO token
     * @param Request $request
     * @return bool
     */
    public static function checkToken(Request $request)
    {
        if (empty($request->header['platform']) || empty($request->header['token'])
            || empty($request->header['client_id']) || empty($request->header['pure'])) {
            return false;
        }
        if (ConfigCrypto::get('io_token') !== $request->header['token']) {
            return false;
        }
        $token = strtolower(trim($request->header['user_agent'] . $request->header['platform'] . $request->header['client_id'] /*. $request->body*/));
        $sha256 = hash_hmac('sha256', $token, ConfigCrypto::get('io_token_secret'));
        if (!$sha256 || $request->header['pure'] !== $sha256) {
            return false;
        }
        return true;
    }

    /**
     * 处理input
     * @param Request $request
     * @return bool
     */
    public static function input(Request $request)
    {
        return self::decrypt(Str::replaceFirst(self::protocol(), '', $request->body));
    }

    /**
     * 处理request
     * @param Request $request
     * @return bool
     */
    public static function response(Request $request)
    {
        return self::encrypt(Str::replaceFirst(self::protocol(), '', $request->body));
    }

}