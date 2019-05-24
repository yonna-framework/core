<?php

namespace PhpureCore\Config;

use PhpureCore\Glue\Response;
use PhpureCore\Mapping\DBType;

class Database extends Arrow
{

    private static $name = 'database';

    /**
     * @param string $tag
     * @param string $host
     * @param string $port
     * @param string $account
     * @param string $password
     * @param string $name
     * @param string $charset
     * @param string $type
     */
    private static function set(string $tag, string $host, string $port, string $account, string $password, string $name, string $charset, string $type)
    {
        if (empty($type)) Response::exception('no type');
        if (empty($host)) Response::exception('no host');
        if (empty($port)) Response::exception('no port');
        static::$stack[static::$name][$tag] = [
            'type' => $type,
            'host' => $host,
            'port' => $port,
            'account' => $account,
            'password' => $password,
            'name' => $name,
            'charset' => $charset,
        ];
    }

    public static function mysql(string $tag, string $host, string $port, string $account, string $password, string $name, string $charset)
    {
        static::set($tag, $host, $port, $account, $password, $name, $charset, DBType::MYSQL);
    }

    public static function pgsql(string $tag, string $host, string $port, string $account, string $password, string $name, string $charset)
    {
        static::set($tag, $host, $port, $account, $password, $name, $charset, DBType::PGSQL);
    }

    public static function mssql(string $tag, string $host, string $port, string $account, string $password, string $name, string $charset)
    {
        static::set($tag, $host, $port, $account, $password, $name, $charset, DBType::MSSQL);
    }

    public static function sqlite(string $tag, string $host, string $port, string $account, string $password, string $name, string $charset)
    {
        static::set($tag, $host, $port, $account, $password, $name, $charset, DBType::SQLITE);
    }

    public static function mongo(string $tag, string $host, string $port, string $account, string $password, string $name, string $charset)
    {
        static::set($tag, $host, $port, $account, $password, $name, $charset, DBType::MONGO);
    }

    public static function redis(string $tag, string $host, string $port, string $account, string $password, string $name, string $charset)
    {
        static::set($tag, $host, $port, $account, $password, $name, $charset, DBType::REDIS);
    }

}