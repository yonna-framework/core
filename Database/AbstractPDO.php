<?php

namespace PhpureCore\Database;

use PDO;
use PDOException;
use PDOStatement;

abstract class AbstractPDO extends AbstractDB
{

    /**
     * pdo 实例
     *
     * @var PDO
     */
    private $pdo;

    /**
     * pdo sQuery
     *
     * @var PDOStatement
     */
    private $PDOStatement;

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        $this->pdoFree();
        $this->pdoClose();
        parent::__destruct();
    }

    /**
     * 获取 PDO
     * @param $type
     * @return PDO
     */
    private function pdo($type)
    {
        if (!$this->pdo) {
            try {
                $this->pdo = new PDO($this->dsn($type), $this->settings["user"], $this->settings["password"],
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->settings['charset'],
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_STRINGIFY_FETCHES => false,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    )
                );
            } catch (PDOException $e) {
                exit($e->getMessage());
            }
        }
        return $this->pdo;
    }

    /**
     * 关闭 PDOState
     */
    private function pdoFree()
    {
        if (!empty($this->PDOStatement)) {
            $this->PDOStatement = null;
        }
    }

    /**
     * 关闭 PDO连接
     */
    private function pdoClose()
    {
        $this->pdo = null;
    }

}
