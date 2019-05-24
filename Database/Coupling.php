<?php

namespace PhpureCore\Database;

use PhpureCore\Core;
use PhpureCore\Glue\Response;

class Coupling
{

    private static $config = array();
    private static $db = array();
    private static $transTrace = array();

    public function __construct(array $config = [])
    {
        static::$config = $config;
        $dbKeys = array_keys($config);
        array_walk($dbKeys, function ($key) {
            static::$transTrace[strtoupper($key)] = 0;
        });
    }

    /**
     * 连接数据库
     * @param string | array $conf
     * @return Mysql | Pgsql | Mssql | Sqlite
     */
    public function connect($conf = 'default'): object
    {
        $link = array();
        if (is_string($conf)) {
            $conf = static::$config[$conf];
        }
        if (is_array($conf)) {
            $link['type'] = $conf['type'] ?? null;
            $link['host'] = $conf['host'] ?? null;
            $link['port'] = $conf['port'] ?? null;
            $link['account'] = $conf['account'] ?? null;
            $link['password'] = $conf['password'] ?? null;
            $link['name'] = $conf['name'] ?? null;
            $link['charset'] = $conf['charset'] ?? null;
            $link['file'] = $conf['file'] ?? null;
        }
        if (empty($link['type'])) Response::exception('Lack type of database');
        if (empty($link['host']) || empty($link['port'])) Response::exception('Lack of host/port address');
        $u = md5(var_export($link, true));
        if (empty(static::$db[$u])) {
            $dbClassName = "\\PhpureCore\\Database\\{$link['type']}";
            switch ($link['type']) {
                case 'Sqlite':
                    static::$db[$u] = Core::singleton(
                        $dbClassName,
                        $link['file'], $link['name'], $link['charset']
                    );
                    break;
                case 'Redis':

                    break;
                case 'Mongo':
                case 'Mysql':
                case 'Pgsql':
                case 'Mssql':
                default:
                    static::$db[$u] = Core::singleton(
                        $dbClassName,
                        $link['host'], $link['port'], $link['account'],
                        $link['password'], $link['name'], $link['charset']
                    );
                    break;
            }
        }
        return static::$db[$u];
    }

}