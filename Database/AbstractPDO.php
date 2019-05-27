<?php

namespace PhpureCore\Database;

use Exception;
use Moment;
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
    protected $options = array();

    /**
     * 临时字段寄存
     */
    protected $currentFieldType = array();
    protected $tempFieldType = array();

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
    public function __destruct()
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
        $this->options = array();
        $this->parameters = array();
        $this->lastSql = '';
        $this->currentFieldType = array();
        $this->tempFieldType = array();
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
                    case DBType::PGSQL:
                        $this->pdo = new PDO($this->dsn(), $this->account, $this->password,
                            array(
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

    /**
     * 返回 lastInsertId
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->pdo()->lastInsertId();
    }

    /**
     * 返回最后一条执行的 sql
     *
     * @return  string
     */
    public function lastSQL()
    {
        return $this->lastSql;
    }

    /**
     * 执行
     *
     * @param string $query
     * @return bool|PDOStatement
     * @throws PDOException
     */
    protected function execute($query)
    {
        $this->pdoFree();
        try {
            $PDOStatement = $this->pdo()->prepare($query);
            if (!empty($this->parameters)) {
                foreach ($this->parameters as $param) {
                    $parameters = explode("\x7F", $param);
                    $PDOStatement->bindParam($parameters[0], $parameters[1]);
                }
            }
            $PDOStatement->execute();
        } catch (PDOException $e) {
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                $this->pdoClose();
                try {
                    $PDOStatement = $this->pdo()->prepare($query);
                    if (!empty($this->parameters)) {
                        foreach ($this->parameters as $param) {
                            $parameters = explode("\x7F", $param);
                            $PDOStatement->bindParam($parameters[0], $parameters[1]);
                        }
                    }
                    $PDOStatement->execute();
                } catch (PDOException $ex) {
                    return $this->error($ex);
                }
            } else {
                $msg = $e->getMessage();
                $err_msg = "[" . (int)$e->getCode() . "]SQL:" . $query . " " . $msg;
                return $this->error($err_msg);
            }
        }
        $this->parameters = array();
        return $PDOStatement;
    }

    /**
     * 获取表字段类型
     * @param $table
     * @return mixed|null
     */
    protected function getFieldType($table = null)
    {
        if (!$table) return $this->currentFieldType;
        if (empty($this->tempFieldType[$table])) {
            $alia = false;
            $originTable = null;
            if (!empty($this->options['alia'][$table])) {
                $originTable = $table;
                $table = $this->options['alia'][$table];
                $alia = true;
            }
            $result = null;
            switch ($this->db_type) {
                case DBType::MYSQL:
                    $sql = "SELECT COLUMN_NAME AS `field`,DATA_TYPE AS fieldtype FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema ='{$this->name}' AND table_name = '{$table}';";
                    $result = Cache::get($sql);
                    if (!$result) {
                        $PDOStatement = $this->execute($sql);
                        if ($PDOStatement) {
                            $result = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
                            Cache::set($sql, $result, 600);
                        }
                    }
                    break;
                case DBType::PGSQL:
                    $sql = "SELECT a.attname as field,format_type(a.atttypid,a.atttypmod) as fieldtype FROM pg_class as c,pg_attribute as a where a.attisdropped = false and c.relname = '{$table}' and a.attrelid = c.oid and a.attnum>0;";
                    $result = Cache::get($sql);
                    if (!$result) {
                        $PDOStatement = $this->execute($sql);
                        if ($PDOStatement) {
                            $result = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
                            Cache::set($sql, $result, 600);
                        }
                    }
                    break;
                case DBType::MSSQL:
                    $sql = "sp_columns \"{$table}\";";
                    $result = Cache::get($sql);
                    if (!$result) {
                        $PDOStatement = $this->execute($sql);
                        if ($PDOStatement) {
                            $result = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
                            Cache::set($sql, $result, 600);
                        }
                    }
                    break;
                case DBType::SQLITE:
                    $sql = "select sql from sqlite_master where tbl_name = '{$table}' and type='table';";
                    $result = Cache::get($sql);
                    if (!$result) {
                        $PDOStatement = $this->execute($sql);
                        if ($PDOStatement) {
                            $result = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
                            $result = reset($result)['sql'];
                            $result = trim(str_replace(["CREATE TABLE {$table}", "create table {$table}"], '', $result));
                            $result = substr($result, 1, strlen($result) - 1);
                            $result = substr($result, 0, strlen($result) - 1);
                            $result = explode(',', $result);
                            $fields = array();
                            foreach ($result as $v) {
                                $v = explode(' ', trim($v));
                                $fields[] = array(
                                    'field' => $v[0],
                                    'fieldtype' => strtolower($v[1]),
                                );
                            }
                            Cache::set($sql, $result, 600);
                        }
                    }
                    break;
                default:
                    Response::exception("{$this->db_type} type is not supported for the time being");
                    break;
            }
            if (!$result) Response::exception("{$this->db_type} get type fail");
            $ft = array();
            foreach ($result as $v) {
                if ($alia && $originTable) {
                    $ft[$originTable . '_' . $v['field']] = $v['fieldtype'];
                } else {
                    $ft[$table . '_' . $v['field']] = $v['fieldtype'];
                }
            }
            $this->tempFieldType[$table] = $ft;
            $this->currentFieldType = array_merge($this->currentFieldType, $ft);
        }
        return $this->currentFieldType;
    }

    /**
     * @param $val
     * @return array
     */
    protected function parseKSort(&$val)
    {
        if (is_array($val)) {
            ksort($val);
            foreach ($val as $k => $v) {
                $val[$k] = $this->parseKSort($v);
            }
        }
        return $val;
    }

    /**
     * @param $val
     * @param $ft
     * @return array|bool|false|int|string
     */
    protected function parseValueByFieldType($val, $ft)
    {
        if (!in_array($ft, ['json', 'jsonb']) && is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = $this->parseValueByFieldType($v, $ft);
            }
            return $val;
        }
        switch ($ft) {
            case 'tinyint':
            case 'smallint':
            case 'int':
            case 'integer':
            case 'bigint':
                $val = intval($val);
                break;
            case 'boolean':
                $val = boolval($val);
                break;
            case 'json':
            case 'jsonb':
                $val = json_encode($val);
                if ($this->isUseCrypto()) {
                    $json = array('crypto' => Crypto::encrypt($val));
                    $val = json_encode($json);
                }
                if ($this->db_type === DBType::MYSQL) {
                    $val = addslashes($val);
                }
                break;
            case 'date':
                $val = date('Y-m-d', strtotime($val));
                break;
            case 'timestamp without time zone':
                $val = date('Y-m-d H:i:s.u', strtotime($val));
                break;
            case 'timestamp with time zone':
                $val = date('Y-m-d H:i:s.u', strtotime($val)) . substr(date('O', strtotime($val)), 0, 3);
                break;
            case 'money':
            case 'numeric':
            case 'decimal':
                $val = round($val, 10);
                break;
            case 'char':
            case 'varchar':
            case 'text':
                $val = trim($val);
                if ($this->isUseCrypto()) {
                    $val = Crypto::encrypt($val);
                }
                break;
            default:
                break;
        }
        if (strpos($ft, 'numeric') !== false) {
            $val = round($val, 10);
        }
        return $val;
    }

    protected function parseWhereByFieldType($val, $ft)
    {
        if (!in_array($ft, ['json', 'jsonb']) && is_array($val)) {
            foreach ($val as $k => $v) {
                $val[$k] = $this->parseWhereByFieldType($v, $ft);
            }
            return $val;
        }
        switch ($ft) {
            case 'tinyint':
            case 'smallint':
            case 'int':
            case 'integer':
            case 'bigint':
                $val = intval($val);
                break;
            case 'boolean':
                $val = boolval($val);
                break;
            case 'date':
                $val = date('Y-m-d', strtotime($val));
                break;
            case 'timestamp without time zone':
                $val = Moment::datetimeMicro('Y-m-d H:i:s', $val);
                break;
            case 'timestamp with time zone':
                $val = Moment::datetimeMicro('Y-m-d H:i:s', $val) . substr(date('O', strtotime($val)), 0, 3);
                break;
            case 'money':
            case 'numeric':
            case 'decimal':
                $val = round($val, 10);
                break;
            case 'char':
            case 'varchar':
            case 'text':
                $val = trim($val);
                if ($this->isUseCrypto()) {
                    $val = Crypto::encrypt($val);
                }
                break;
            default:
                break;
        }
        if (strpos($ft, 'numeric') !== false) {
            $val = round($val, 10);
        }
        return $val;
    }

    /**
     * 数组转逗号形式序列(实质上是一个逗号序列，运用 not / contains(find_in_set) 查询)
     * @param $arr
     * @param $type
     * @return mixed
     * 析构方法
     * @access public
     */
    protected function arr2comma($arr, $type)
    {
        if ($type && is_array($arr)) {
            if ($arr) {
                foreach ($arr as $ak => $a) {
                    $arr[$ak] = $this->parseValueByFieldType($a, $type);
                }
                $arr = ',,,,,' . implode(',', $arr);
            } else {
                $arr = null;
            }
        }
        return $arr;
    }

    /**
     * 逗号序列转回数组(实质上是一个逗号序列，运用 not / contains 查询)
     * @param $arr
     * @param $type
     * @return mixed
     */
    protected function comma2arr($arr, $type)
    {
        if ($type && is_string($arr)) {
            if ($arr) {
                $arr = str_replace(',,,,,', '', $arr);
                $arr = explode(',', $arr);
                if ($this->isUseCrypto()) {
                    foreach ($arr as $ak => $a) {
                        $arr[$ak] = Crypto::decrypt($a);
                    }
                }
            } else {
                $arr = array();
            }
        }
        return $arr;
    }

    /**
     * 数组转 pg 形式数组
     * @param $arr
     * @param $type
     * @return mixed
     */
    protected function toPGArray($arr, $type)
    {
        if ($type && is_array($arr)) {
            if ($arr) {
                foreach ($arr as $ak => $a) {
                    $arr[$ak] = $this->parseValueByFieldType($a, $type);
                }
                $arr = '{' . implode(',', $arr) . '}';
            } else {
                $arr = '{}';
            }
        }
        return $arr;
    }

    /**
     * 递归式格式化数据
     * @param $result
     * @return mixed
     */
    protected function fetchFormat($result)
    {
        $ft = $this->getFieldType();
        if ($ft) {
            foreach ($result as $k => $v) {
                if (is_array($v)) {
                    $result[$k] = $this->fetchFormat($v);
                } elseif (isset($ft[$k])) {
                    switch ($ft[$k]) {
                        case 'json':
                            $result[$k] = json_decode($v, true);
                            if ($this->isUseCrypto()) {
                                $crypto = $result[$k]['crypto'] ?? '';
                                $crypto = Crypto::decrypt($crypto);
                                $result[$k] = json_decode($crypto, true);
                            }
                            $result[$k] = $this->parseKSort($result[$k]);
                            break;
                        case 'tinyint':
                        case 'bigint':
                        case 'smallint':
                        case 'int':
                            $result[$k] = intval($v);
                            break;
                        case 'numeric':
                        case 'decimal':
                            $result[$k] = round($v, 10);
                            break;
                        case 'char':
                        case 'varchar':
                        case 'text':
                            if (strpos($v, ',,,,,') === false && $this->isUseCrypto()) {
                                $result[$k] = Crypto::decrypt($v);
                            }
                            break;
                        default:
                            break;
                    }
                    if (strpos($v, ',,,,,') === 0) {
                        $result[$k] = $this->comma2arr($v, $ft);
                    }
                    if ($this->db_type === DBType::PGSQL) {
                        if ($ft[$k] == 'json' || $ft[$k] == 'jsonb' || strpos($ft[$k], '[]') !== false) {
                            $result[$k] = json_decode($v, true);
                            if ($this->isUseCrypto()) {
                                $crypto = $result[$k]['crypto'] ?? '';
                                $crypto = Crypto::decrypt($crypto);
                                $result[$k] = json_decode($crypto, true);
                            }
                            $result[$k] = $this->parseKSort($result[$k]);
                        } elseif (strpos($ft[$k], 'numeric') !== false) {
                            $result[$k] = round($v, 10);
                        } elseif ($ft[$k] === 'money') {
                            $result[$k] = round($v, 10);
                        } elseif (in_array($ft[$k], ['smallint', 'bigint', 'integer'])) {
                            $result[$k] = intval($v);
                        } elseif (in_array($ft[$k], ['text', 'char'])) {
                            if ($this->isUseCrypto()) {
                                $result[$k] = Crypto::decrypt($v);
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 执行 SQL
     *
     * @param string $query
     * @param int $fetchMode
     * @return mixed
     * @throws Exception
     */
    public function query($query = '', $fetchMode = PDO::FETCH_ASSOC)
    {
        $table = $this->getTable();
        if (!$table) {
            throw new Exception('lose table');
        }
        $query = trim($query);
        $this->lastSql = $query;

        $rawStatement = explode(" ", $query);
        $statement = strtolower(trim($rawStatement[0]));
        //read model,check cache
        if ($statement === 'select' || $statement === 'show') {
            $result = false;
            if ($this->getRedisType() === 'forever') {
                $result = $this->redis()->hGet($table, $query);
            } elseif (is_numeric($this->getRedisType())) {
                $result = $this->redis()->get($table . $query);
            }
            if ($result) return $result;
        }
        //释放前次的查询结果
        if (!$this->PDOStatement = $this->execute($query)) {
            throw new Exception($this->getError());
        }

        if ($statement === 'select' || $statement === 'show') {
            $result = $this->PDOStatement->fetchAll($fetchMode);
            $result = $this->fetchFormat($result);
            if ($this->getRedisType() === 'forever') {
                $this->redis()->hSet($table, $query, $result);
            } elseif (is_numeric($this->getRedisType())) {
                $this->redis()->set($table . $query, $result, (int)$this->getRedisType());
            }
            return $result;
        } elseif ($statement === 'update' || $statement === 'delete') {
            if ($this->getRedisType() === 'forever') $this->redis()->delete($table);
            return $this->PDOStatement->rowCount();
        } elseif ($statement === 'insert') {
            if ($this->getRedisType() === 'forever') $this->redis()->delete($table);
            return $this->PDOStatement->rowCount();
        } else {
            return null;
        }
    }

}
