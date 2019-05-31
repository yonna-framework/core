<?php

namespace PhpureCore\Database;

use PhpureCore\Config\Arrow;
use PhpureCore\Core;
use PhpureCore\Glue\Response;

class Coupling
{

    private static $config = null;
    private static $db = array();
    private static $transTrace = array();

    /**
     * 连接数据库
     * @param string | array $conf
     * @param null $dbtc db type check
     * @return Mysql|Pgsql|Mssql|Sqlite|Mongo|Redis
     */
    public static function connect($conf = 'default', $dbtc = null): object
    {
        if (static::$config === null) {
            static::$config = Arrow::fetch()['database'];
            $dbKeys = array_keys(static::$config);
            array_walk($dbKeys, function ($key) {
                static::$transTrace[strtoupper($key)] = 0;
            });
        }
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
            $link['db_file_path'] = $conf['db_file_path'] ?? null;
            $link['auto_cache'] = $conf['auto_cache'] ?? null;
        }
        if (empty($link['type'])) Response::exception('Lack type of database');
        if ($dbtc && $dbtc !== $link['type']) Response::exception('Database type check no pass');
        if (empty($link['host']) || empty($link['port'])) Response::exception('Lack of host/port address');
        $u = md5(var_export($link, true));
        if (empty(static::$db[$u])) {
            static::$db[$u] = Core::singleton("\\PhpureCore\\Database\\{$link['type']}", $link);
        }
        return static::$db[$u];
    }

}