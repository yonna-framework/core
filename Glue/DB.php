<?php


namespace PhpureCore\Glue;

use PhpureCore\Core\Glue;
use PhpureCore\Database\Coupling;

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

}
