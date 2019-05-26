<?php

namespace PhpureCore\Database;

use PDO;
use PDOException;
use PDOStatement;
use PhpureCore\Glue\Response;
use PhpureCore\Mapping\DBType;

abstract class AbstractPDO extends AbstractDB
{

    /**
     * pdo 实例
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * pdo sQuery
     *
     * @var PDOStatement
     */
    protected $PDOStatement;

    /**
     * sql 的参数
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * 参数
     *
     * @var array
     */
    protected $_options = array();

    /**
     * 临时字段寄存
     */
    protected $_currentFieldType = array();
    protected $_tempFieldType = array();

    /**
     * 最后一条执行的 sql
     *
     * @var string
     */
    protected $lastSql = '';

    /**
     * 多重嵌套事务处理堆栈
     */
    protected $_transTrace = 0;

    /**
     * 析构方法
     * @access public
     */
    protected function __destruct()
    {
        $this->pdoFree();
        $this->pdoClose();
        parent::__destruct();
    }

    /**
     * 清除所有数据
     */
    protected function resetAll()
    {
        $this->_options = array();
        $this->parameters = array();
        $this->lastSql = '';
        $this->_currentFieldType = array();
        $this->_tempFieldType = array();
        $this->_where = array();
        $this->_where_table = '';
        parent::resetAll();
    }

    /**
     * 获取数据库错误信息
     * @return mixed
     */
    protected function getError()
    {
        $error = $this->getError();
        if (!$error) {
            if ($this->pdo) {
                $errorInfo = $this->pdo->errorInfo();
                $error = $errorInfo[1] . ':' . $errorInfo[2];
            }
            if ('' != $this->lastSql) {
                $error .= "\n [ SQL ] : " . $this->lastSql;
            }
        }
        return $error;
    }

    /**
     * 获取 PDO
     * @return PDO
     */
    protected function pdo()
    {
        if (!$this->pdo) {
            try {
                switch ($this->db_type) {
                    case DBType::MYSQL:
                        $this->pdo = new PDO($this->dsn(), $this->account, $this->password,
                            array(
                                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->charset,
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_STRINGIFY_FETCHES => false,
                                PDO::ATTR_EMULATE_PREPARES => false,
                            )
                        );
                        break;
                    default:
                        Response::exception("{$this->db_type} type is not supported for the time being");
                        break;
                }
            } catch (PDOException $e) {
                exit($e->getMessage());
            }
        }
        return $this->pdo;
    }

    /**
     * 关闭 PDOState
     */
    protected function pdoFree()
    {
        if (!empty($this->PDOStatement)) {
            $this->PDOStatement = null;
        }
    }

    /**
     * 关闭 PDO连接
     */
    protected function pdoClose()
    {
        $this->pdo = null;
    }

}
