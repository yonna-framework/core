<?php

namespace PhpureCore\Config;

use PhpureCore\Glue\Response;
use PhpureCore\Mapping\AutoCache;
use PhpureCore\Mapping\DBType;

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
        $auto_cache = isset($setting['auto_cache']) ? strtolower($setting['auto_cache'])  : AutoCache::FALSE;
        // check
        if (empty($type)) Response::exception('no type');
        if ($type === DBType::MYSQL || $type === DBType::PGSQL || $type === DBType::MSSQL || $type === DBType::MONGO || $type === DBType::REDIS) {
            if (empty($host)) Response::exception('no host');
            if (empty($port)) Response::exception('no port');
        }
        if ($type === DBType::MYSQL || $type === DBType::PGSQL || $type === DBType::MSSQL) {
            if (empty($account)) Response::exception('no account');
            if (empty($password)) Response::exception('no password');
        }
        if ($type === DBType::SQLITE) {
            if (empty($db_file_path)) Response::exception('no db file path');
        }
        // cache
        if ($auto_cache === 'true' || $auto_cache === 'false') {
            $auto_cache = $auto_cache === 'true';
        }
        elseif (is_numeric($auto_cache)) {
            $auto_cache = (int)$auto_cache;
            if($auto_cache < 10) $auto_cache = 10;
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
            'auto_cache' => $auto_cache,
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