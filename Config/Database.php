<?php

namespace Yonna\Config;

use Yonna\Exception\Exception;
use Yonna\Mapping\DBType;
use System;

class Database extends Arrow
{

    private static $name = 'database';

    /**
     * @param string $tag
     * @param array $setting
     */
    private static function set(string $tag, array $setting)
    {
        $type = $setting['type'] ?? null;
        $host = $setting['host'] ?? null;
        $port = $setting['port'] ?? null;
        $account = $setting['account'] ?? null;
        $password = $setting['password'] ?? null;
        $name = $setting['name'] ?? null;
        $charset = $setting['charset'] ?? null;
        $db_file_path = $setting['db_file_path'] ?? null;
        $project_key = isset($setting['project_key']) ? strtolower($setting['project_key']) : null;
        $auto_cache = isset($setting['auto_cache']) ? strtolower($setting['auto_cache']) : false;
        $auto_crypto = isset($setting['auto_crypto']) ? strtolower($setting['auto_crypto']) : false;
        $crypto_type = $setting['crypto_type'] ?? null;
        $crypto_secret = $setting['crypto_secret'] ?? null;
        $crypto_iv = $setting['crypto_iv'] ?? null;
        // check
        if (empty($type)) Exception::throw('no type');
        if ($type === DBType::MYSQL || $type === DBType::PGSQL || $type === DBType::MSSQL || $type === DBType::MONGO || $type === DBType::REDIS) {
            if (empty($host)) Exception::throw('no host');
            if (empty($port)) Exception::throw('no port');
        }
        if ($type === DBType::MYSQL || $type === DBType::PGSQL || $type === DBType::MSSQL) {
            if (empty($account)) Exception::throw('no account');
            if (empty($password)) Exception::throw('no password');
        }
        if ($type === DBType::SQLITE) {
            if (empty($db_file_path)) Exception::throw('no db file path');
        }
        // auto_cache
        if ($auto_cache === 'true' || $auto_cache === 'false') {
            $auto_cache = $auto_cache === 'true';
        } elseif (is_numeric($auto_cache)) {
            $auto_cache = (int)$auto_cache;
        }
        // auto_crypto
        if ($auto_crypto === 'true' || $auto_crypto === 'false') {
            $auto_crypto = $auto_crypto === 'true';
            if ($auto_crypto === true) {
                if (empty($crypto_type)) Exception::throw('no crypto_type');
                if (empty($crypto_secret)) Exception::throw('no crypto_secret');
                if (empty($crypto_iv)) Exception::throw('no crypto_iv');
                if (!in_array($crypto_type, System::getOpensslCipherMethods())) {
                    Exception::throw("OpensslCipherMethods not support this type: {$crypto_type}");
                }
            }
        }
        static::$stack[static::$name][$tag] = [
            'type' => $type,
            'host' => $host,
            'port' => $port,
            'account' => $account,
            'password' => $password,
            'name' => $name,
            'charset' => $charset,
            'db_file_path' => $db_file_path,
            'project_key' => $project_key,
            'auto_cache' => $auto_cache,
            'auto_crypto' => $auto_crypto,
            'crypto_type' => $crypto_type,
            'crypto_secret' => $crypto_secret,
            'crypto_iv' => $crypto_iv,
        ];
    }

    public static function mysql(string $tag, array $setting)
    {
        $setting['type'] = DBType::MYSQL;
        static::set($tag, $setting);
    }

    public static function pgsql(string $tag, array $setting)
    {
        $setting['type'] = DBType::PGSQL;
        static::set($tag, $setting);
    }

    public static function mssql(string $tag, array $setting)
    {
        $setting['type'] = DBType::MSSQL;
        static::set($tag, $setting);
    }

    public static function sqlite(string $tag, array $setting)
    {
        $setting['type'] = DBType::SQLITE;
        static::set($tag, $setting);
    }

    public static function mongo(string $tag, array $setting)
    {
        $setting['type'] = DBType::MONGO;
        static::set($tag, $setting);
    }

    public static function redis(string $tag, array $setting)
    {
        $setting['type'] = DBType::REDIS;
        $setting['auto_cache'] = 'false';
        static::set($tag, $setting);
    }

}