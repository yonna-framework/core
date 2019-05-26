<?php


namespace PhpureCore\Glue;

use PhpureCore\Core\Glue;
use PhpureCore\Database\Coupling;
use PhpureCore\Mapping\DBType;

/**
 * Class DB
 */
class DB extends Glue
{

    /**
     * @param string $conf
     * @return object|\PhpureCore\Database\Mongo|\PhpureCore\Database\Mssql|\PhpureCore\Database\Mysql|\PhpureCore\Database\Pgsql|\PhpureCore\Database\Redis|\PhpureCore\Database\Sqlite
     */
    public static function connect($conf = 'default')
    {
        return Coupling::connect($conf);
    }

    /**
     * @param string $conf
     * @return \PhpureCore\Database\Mysql
     */
    public static function mysql($conf = 'mysql')
    {
        return Coupling::connect($conf, DBType::MYSQL);
    }

    /**
     * @param string $conf
     * @return \PhpureCore\Database\Pgsql
     */
    public static function pgsql($conf = 'pgsql')
    {
        return Coupling::connect($conf, DBType::PGSQL);
    }

    /**
     * @param string $conf
     * @return \PhpureCore\Database\Mssql
     */
    public static function mssql($conf = 'mssql')
    {
        return Coupling::connect($conf, DBType::MSSQL);
    }

    /**
     * @param string $conf
     * @return \PhpureCore\Database\Sqlite
     */
    public static function sqlite($conf = 'sqlite')
    {
        return Coupling::connect($conf, DBType::SQLITE);
    }

    /**
     * @param string $conf
     * @return \PhpureCore\Database\Mongo
     */
    public static function mongo($conf = 'mongo')
    {
        return Coupling::connect($conf, DBType::MONGO);
    }

    /**
     * @param string $conf
     * @return \PhpureCore\Database\Redis
     */
    public static function redis($conf = 'redis')
    {
        return Coupling::connect($conf, DBType::REDIS);
    }

}
