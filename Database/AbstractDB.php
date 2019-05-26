<?php

namespace PhpureCore\Database;

use PhpureCore\Glue\Response;
use PhpureCore\Mapping\DBType;

abstract class AbstractDB
{

    private $host = '';
    private $port = '';
    private $account = '';
    private $password = '';
    private $name = '';
    private $charset = '';
    private $project_key = '';

    /**
     * dsn 链接串
     *
     * @var string
     */
    private $dsn;

    /**
     * 排序类型设置
     */
    const DESC = 'desc';
    const ASC = 'asc';


    /**
     * 是否对内容加密
     * @var bool
     */
    private $use_crypto = false;





    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        // nothing
    }



    /**
     * 获取 DSN
     * @param string $type
     * @return string
     */
    protected function dsn(string $type)
    {
        if (empty($type)) Response::exception('Dsn type is Empty');
        if (!$this->dsn) {
            switch ($type){
                case DBType::MYSQL:
                    $this->dsn = 'mysql:dbname=' . $this->settings["name"] . ';host=' . $this->settings["host"] . ';port=' . $this->settings['port'];
                    break;
            }
        }
        return $this->dsn;
    }


    /**
     * @return bool
     */
    protected function isUseCrypto(): bool
    {
        return $this->use_crypto;
    }

    /**
     * @tips 一旦设为加密则只能全字而无法模糊匹配
     * @param bool $use_crypto
     * @return AbstractDB|Mysql|Pgsql|Mssql|Sqlite|Mongo|Redis
     */
    protected function setUseCrypto(bool $use_crypto)
    {
        $this->use_crypto = $use_crypto;
        return $this;
    }


}
