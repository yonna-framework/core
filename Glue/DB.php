<?php


namespace Yonna\Glue;

use Yonna\Core\Glue;
use Yonna\Database\Coupling;
use Yonna\Mapping\DBType;

/**
 * Class DB
 */
class DB extends Glue
{

    /**
     * @param string $conf
     * @return object|\Yonna\Database\Mongo|\Yonna\Database\Mssql|\Yonna\Database\Mysql|\Yonna\Database\Pgsql|\Yonna\Database\Redis|\Yonna\Database\Sqlite
     */
    public static function connect($conf = 'default')
    {
        return Coupling::connect($conf);
    }

    /**
     * @param string $conf
     * @return \Yonna\Database\Mysql
     */
    public static function mysql($conf = 'mysql')
    {
        return Coupling::connect($conf, DBType::MYSQL);
    }

    /**
     * @param string $conf
     * @return \Yonna\Database\Pgsql
     */
    public static function pgsql($conf = 'pgsql')
    {
        return Coupling::connect($conf, DBType::PGSQL);
    }

    /**
     * @param string $conf
     * @return \Yonna\Database\Mssql
     */
    public static function mssql($conf = 'mssql')
    {
        return Coupling::connect($conf, DBType::MSSQL);
    }

    /**
     * @param string $conf
     * @return \Yonna\Database\Sqlite
     */
    public static function sqlite($conf = 'sqlite')
    {
        return Coupling::connect($conf, DBType::SQLITE);
    }

    /**
     * @param string $conf
     * @return \Yonna\Database\Mongo
     */
    public static function mongo($conf = 'mongo')
    {
        return Coupling::connect($conf, DBType::MONGO);
    }

    /**
     * @param string $conf
     * @return \Yonna\Database\Redis
     */
    public static function redis($conf = 'redis')
    {
        return Coupling::connect($conf, DBType::REDIS);
    }

}
