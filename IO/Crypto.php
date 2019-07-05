<?php

namespace Yonna\IO;

use Yonna\Foundation\Str;
use Yonna\Response\Collector;

class Crypto
{

    /**
     * 是否隐秘请求
     * @param Request $request
     * @return bool
     */
    private static function isCrypto(Request $request): bool
    {
        return $request->getInputType() === InputType::RAW && strpos($request->getInput(), Config::getCryptoProtocol()) === 0;
    }

    /**
     * @param $str
     * @return string
     */
    private static function encrypt(string $str)
    {
        $type = Config::getCryptoType();
        $secret = Config::getCryptoSecret();
        $iv = Config::getCryptoIv();
        if (!$type || !$secret || !$iv) {
            return $str;
        }
        return openssl_encrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * @param $str
     * @return string
     */
    private static function decrypt(string $str)
    {
        $type = Config::getCryptoType();
        $secret = Config::getCryptoSecret();
        $iv = Config::getCryptoIv();
        if (!$type || !$secret || !$iv) {
            return $str;
        }
        return openssl_decrypt($str, $type, $secret, 0, $iv);
    }

    /**
     * 处理input
     * @param Request $request
     * @return Request
     */
    public static function input(Request $request)
    {
        if (self::isCrypto($request) === false) {
            return $request;
        }
        $request->setInput(self::decrypt(Str::replaceFirst(Config::getCryptoProtocol(), '', $request->getInput())));
        return $request;
    }

    /**
     * 处理output
     * @param Request $request
     * @param Collector $collector
     * @return Collector
     */
    public static function output(Request $request, Collector $collector)
    {
        if (self::isCrypto($request) === false) {
            return $collector;
        }
        $data = ['crypto' => Config::getCryptoProtocol() . self::encrypt(json_encode($collector->getData()))];
        $collector->setData($data);
        return $collector;
    }


}