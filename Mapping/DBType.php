<?php
/**
 * Database Driver Types
 */

namespace PhpureCore\Mapping;

class DBType extends Mapping
{

    const MYSQL = 'Mysql';
    const PGSQL = 'Pgsql';
    const MSSQL = 'Mssql';
    const SQLITE = 'Sqlite';
    const MONGO = 'Mongo';
    const REDIS = 'Redis';


}