<?php

namespace PhpureCore\Database;

use Moment;
use PDO;
use PDOException;
use PDOStatement;
use PhpureCore\Exception\Exception;
use PhpureCore\Mapping\AutoCache;
use PhpureCore\Mapping\DBType;
use Str;

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
     * 查询表达式
     *
     * @var string
     */
    protected $selectSql = null;

    /**
     * 最后一条执行的 sql
     *
     * @var string
     */
    protected $lastSql = '';

    /**
     * 是否不执行sql直接返回sql
     *
     * @var string
     */
    protected $fetchSql = false;

    /**
     * 多重嵌套事务处理堆栈
     */
    protected $transTrace = 0;


    /**
     * where 条件类型设置
     */
    const equalTo = 'equalTo';                              //等于
    const notEqualTo = 'notEqualTo';                        //不等于
    const greaterThan = 'greaterThan';                      //大于
    const greaterThanOrEqualTo = 'greaterThanOrEqualTo';    //大于等于
    const lessThan = 'lessThan';                            //小于
    const lessThanOrEqualTo = 'lessThanOrEqualTo';          //小于等于
    const like = 'like';                                    //包含
    const notLike = 'notLike';                              //不包含
    const isNull = 'isNull';                                //为空
    const isNotNull = 'isNotNull';                          //不为空
    const between = 'between';                              //在值之内
    const notBetween = 'notBetween';                        //在值之外
    const in = 'in';                                        //在或集
    const notIn = 'notIn';                                  //不在或集
    const findInSetOr = 'findInSetOr';                      //findInSetOr (mysql)
    const notFindInSetOr = 'notFindInSetOr';                //notFindInSetOr (mysql)
    const findInSetAnd = 'findInSetAnd';                    //findInSetAnd (mysql)
    const notFindInSetAnd = 'notFindInSetAnd';              //notFindInSetAnd (mysql)
    const any = 'any';                                      //any (pgsql)
    const contains = 'contains';                            //contains (pgsql)
    const isContainsBy = 'isContainsBy';                    //isContainsBy (pgsql)

    /**
     * 构造方法
     *
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        parent::__construct($setting);
        return $this;
    }

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
        $this->fetchSql = false;
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
     * 检查数据库
     * @param $type
     * @param $msg
     * @return mixed
     */
    protected function askDBType($type, $msg)
    {
        if ($this->db_type !== $type) {
            Exception::abort("{$msg} not support {$this->db_type} yet");
        }
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
                    case DBType::MSSQL:
                        $this->pdo = new PDO($this->dsn(), $this->account, $this->password,
                            array(
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            )
                        );
                        break;
                    case DBType::SQLITE:
                        $this->pdo = new PDO($this->dsn(), null, null,
                            array(
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_STRINGIFY_FETCHES => false,
                                PDO::ATTR_EMULATE_PREPARES => false,
                            )
                        );
                        break;
                    default:
                        Exception::abort("{$this->db_type} not support PDO yet");
                        break;
                }
            } catch (PDOException $e) {
                Exception::throw($e->getMessage());
                exit;
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
     * 开始事务
     */
    public function beginTrans()
    {
        if ($this->transTrace <= 0) {
            if ($this->pdo()->inTransaction()) {
                $this->pdo()->commit();
            }
            $this->transTrace = 1;
        } else {
            $this->transTrace++;
            return true;
        }
        try {
            return $this->pdo()->beginTransaction();
        } catch (PDOException $e) {
            // 服务端断开时重连一次
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                $this->pdoClose();
                return $this->pdo()->beginTransaction();
            } else {
                throw $e;
            }
        }
    }

    /**
     * 提交事务
     */
    public function commitTrans()
    {
        $this->transTrace > 0 && $this->transTrace--;
        if ($this->transTrace > 0) {
            return true;
        }
        return $this->pdo()->commit();
    }

    /**
     * 事务回滚
     */
    public function rollBackTrans()
    {
        $this->transTrace > 0 && $this->transTrace--;
        if ($this->transTrace > 0) {
            return true;
        }
        if ($this->pdo()->inTransaction()) {
            return $this->pdo()->rollBack();
        }
        return false;
    }

    /**
     * 检测是否在一个事务内
     * @return bool
     */
    public function inTransaction()
    {
        return $this->pdo()->inTransaction();
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
                            $temp = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
                            $result = array();
                            foreach ($temp as $v) {
                                $result[] = array(
                                    'field' => $v['COLUMN_NAME'],
                                    'fieldtype' => strtolower($v['TYPE_NAME']),
                                );
                            }
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
                            $temp = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);
                            $temp = reset($temp)['sql'];
                            $temp = trim(str_replace(["CREATE TABLE {$table}", "create table {$table}"], '', $temp));
                            $temp = substr($temp, 1, strlen($temp) - 1);
                            $temp = substr($temp, 0, strlen($temp) - 1);
                            $temp = explode(',', $temp);
                            $result = array();
                            foreach ($temp as $v) {
                                $v = explode(' ', trim($v));
                                $result[] = array(
                                    'field' => $v[0],
                                    'fieldtype' => strtolower($v[1]),
                                );
                            }
                            Cache::set($sql, $result, 600);
                        }
                    }
                    break;
                default:
                    Exception::abort("Field Type not support {$this->db_type} yet");
                    break;
            }
            if (!$result) Exception::throw("{$this->db_type} get type fail");
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
     * 字段和表名处理
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey($key)
    {
        $key = trim($key);
        if (!is_numeric($key) && !preg_match('/[,\'\"\*\(\)`.\s]/', $key)) {
            switch ($this->db_type) {
                case DBType::MYSQL:
                    $key = '`' . $key . '`';
                    break;
                case DBType::PGSQL:
                case DBType::MSSQL:
                    $key = '"' . $key . '"';
                    break;
                case DBType::SQLITE:
                    $key = "'" . $key . "'";
                    break;
                default:
                    Exception::throw('parseKey db type error');
                    break;
            }
        }
        return $key;
    }

    /**
     * value分析
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = '\'' . $value . '\'';
        } elseif (is_array($value)) {
            $value = array_map(array($this, 'parseValue'), $value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * field分析
     * @access private
     * @param mixed $fields
     * @return string
     */
    protected function parseField($fields)
    {
        if (is_string($fields) && '' !== $fields) {
            $fields = explode(',', $fields);
        }
        if (is_array($fields)) {
            // 完善数组方式传字段名的支持
            // 支持 'field1'=>'field2' 这样的字段别名定义
            $array = array();
            foreach ($fields as $key => $field) {
                if (!is_numeric($key))
                    $array[] = $this->parseKey($key) . ' AS ' . $this->parseKey($field);
                else
                    $array[] = $this->parseKey($field);
            }
            $fieldsStr = implode(',', $array);
        } else {
            $fieldsStr = '*';
        }
        return $fieldsStr;
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
                    $json = array('crypto' => $this->Crypto::encrypt($val));
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
            case 'smallmoney':
            case 'money':
            case 'numeric':
            case 'decimal':
            case 'float':
            case 'real':
                $val = round($val, 10);
                break;
            case 'char':
            case 'varchar':
            case 'text':
            case 'nchar':
            case 'nvarchar':
            case 'ntext':
                $val = trim($val);
                if ($this->isUseCrypto()) {
                    $val = $this->Crypto::encrypt($val);
                }
                break;
            default:
                if ($this->db_type === DBType::PGSQL) {
                    if (strpos($ft, 'numeric') !== false) {
                        $val = round($val, 10);
                    }
                }
                break;
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
            case 'smallmoney':
            case 'money':
            case 'numeric':
            case 'decimal':
            case 'float':
            case 'real':
                $val = round($val, 10);
                break;
            case 'char':
            case 'varchar':
            case 'text':
            case 'nchar':
            case 'nvarchar':
            case 'ntext':
                $val = trim($val);
                if ($this->isUseCrypto()) {
                    $val = $this->Crypto::encrypt($val);
                }
                break;
            default:
                if ($this->db_type === DBType::PGSQL) {
                    if (strpos($ft, 'numeric') !== false) {
                        $val = round($val, 10);
                    }
                }
                break;
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
                        $arr[$ak] = $this->Crypto::decrypt($a);
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
     * sql过滤
     * @param $sql
     * @return bool
     */
    protected function sqlFilter($sql)
    {
        $result = true;
        if ($sql) {
            if (is_array($sql)) {
                foreach ($sql as $v) {
                    if (!$v) continue;
                    if (is_array($v)) {
                        return $this->sqlFilter($v);
                    } else {
                        $preg = preg_match('/(.*?((select)|(from)|(count)|(delete)|(update)|(drop)|(truncate)).*?)+/i', $v);
                        if ($preg) {
                            $result = false;
                            break;
                        }
                    }
                }
            } else {
                if ($sql) {
                    $result = preg_match('/(.*?((select)|(from)|(count)|(delete)|(update)|(drop)|(truncate)).*?)+/i', $sql) ? false : true;
                }
            }
        }
        return $result;
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
                        case 'jsonb':
                            $result[$k] = json_decode($v, true);
                            if ($this->isUseCrypto()) {
                                $crypto = $result[$k]['crypto'] ?? '';
                                $crypto = $this->Crypto::decrypt($crypto);
                                $result[$k] = json_decode($crypto, true);
                            }
                            $result[$k] = $this->parseKSort($result[$k]);
                            break;
                        case 'tinyint':
                        case 'smallint':
                        case 'int':
                        case 'integer':
                        case 'bigint':
                            $result[$k] = intval($v);
                            break;
                        case 'numeric':
                        case 'decimal':
                        case 'money':
                            $result[$k] = round($v, 10);
                            break;
                        case 'char':
                        case 'varchar':
                        case 'text':
                            if (strpos($v, ',,,,,') === false && $this->isUseCrypto()) {
                                $result[$k] = $this->Crypto::decrypt($v);
                            }
                            break;
                        default:
                            if ($this->db_type === DBType::PGSQL) {
                                if (substr($ft[$k], -2) === '[]') {
                                    $result[$k] = json_decode($v, true);
                                    if ($this->isUseCrypto()) {
                                        $crypto = $result[$k]['crypto'] ?? '';
                                        $crypto = $this->Crypto::decrypt($crypto);
                                        $result[$k] = json_decode($crypto, true);
                                    }
                                    $result[$k] = $this->parseKSort($result[$k]);
                                } elseif (strpos($ft[$k], 'numeric') !== false) {
                                    $result[$k] = round($v, 10);
                                }
                            }
                            break;
                    }
                    if (strpos($v, ',,,,,') === 0) {
                        $result[$k] = $this->comma2arr($v, $ft);
                    }
                }
            }
        }
        return $result;
    }


    /**
     * 分析表达式
     * @access protected
     * @param array $options 表达式参数
     * @return array
     */
    protected function parseOptions($options = array())
    {
        if (empty($this->options['field'])) {
            $this->field('*');
        }
        if (is_array($options)) {
            $options = array_merge($this->options, $options);
        }
        if (!isset($options['table'])) {
            $options['table'] = $this->getTable();
        }
        //别名
        if (!empty($options['alias'])) {
            $options['table'] .= ' ' . $options['alias'];
        }
        return $options;
    }

    /**
     * schemas分析
     * @access private
     * @param mixed $schemas
     * @return string
     */
    protected function parseSchemas($schemas)
    {
        if (is_array($schemas)) {// 支持别名定义
            $array = array();
            foreach ($schemas as $schema => $alias) {
                if (!is_numeric($schema))
                    $array[] = $this->parseKey($schema) . ' ' . $this->parseKey($alias);
                else
                    $array[] = $this->parseKey($alias);
            }
            $schemas = $array;
        } elseif (is_string($schemas)) {
            $schemas = explode(',', $schemas);
            return $this->parseSchemas($schemas);
        }
        return implode(',', $schemas);
    }

    /**
     * table分析
     * @access private
     * @param mixed $tables
     * @return string
     */
    protected function parseTable($tables)
    {
        if (!$tables) Exception::throw('no table');
        if (is_array($tables)) {// 支持别名定义
            $array = array();
            foreach ($tables as $table => $alias) {
                if (!is_numeric($table))
                    $array[] = $this->parseKey($table) . ' ' . $this->parseKey($alias);
                else
                    $array[] = $this->parseKey($alias);
            }
            $tables = $array;
        } elseif (is_string($tables)) {
            $tables = explode(',', $tables);
            return $this->parseTable($tables);
        }
        return implode(',', $tables);
    }

    /**
     * limit分析
     * @access private
     * @param mixed $limit
     * @return string
     */
    protected function parseLimit($limit)
    {
        $l = '';
        switch ($this->db_type) {
            case DBType::MSSQL:
                if (!empty($this->options['offset'])) {
                    return $l;
                }
                $l = !empty($limit) ? ' TOP ' . $limit . ' ' : '';
                break;
            default:
                $l = !empty($limit) ? ' LIMIT ' . $limit . ' ' : '';
                break;
        }
        return $l;
    }

    /**
     * offset分析
     * @access private
     * @param mixed $offset
     * @return string
     */
    protected function parseOffset($offset)
    {
        if ($offset > 0 || $offset === 0) {
            if (empty($this->options['order'])) {
                Exception::throw('OFFSET should used ORDER BY');
            }
            return " offset {$offset} rows fetch next {$this->options['limit']} rows only";
        }
        return '';
    }

    /**
     * join分析
     * @access private
     * @param mixed $join
     * @return string
     */
    protected function parseJoin($join)
    {
        $joinStr = '';
        if (!empty($join)) {
            $joinStr = ' ' . implode(' ', $join) . ' ';
        }
        return $joinStr;
    }

    /**
     * order分析
     * @access private
     * @param mixed $order
     * @return string
     */
    protected function parseOrderBy($order)
    {
        if (is_array($order)) {
            $array = array();
            foreach ($order as $key => $val) {
                if (is_numeric($key)) {
                    $array[] = $this->parseKey($val);
                } else {
                    $array[] = $this->parseKey($key) . ' ' . $val;
                }
            }
            $order = implode(',', $array);
        }
        return !empty($order) ? ' ORDER BY ' . $order : '';
    }

    /**
     * group分析
     * @access private
     * @param mixed $group
     * @return string
     */
    protected function parseGroupBy($group)
    {
        return !empty($group) ? ' GROUP BY ' . $group : '';
    }

    /**
     * having分析
     * @access private
     * @param string $having
     * @return string
     */
    protected function parseHaving($having)
    {
        return !empty($having) ? ' HAVING ' . $having : '';
    }

    /**
     * comment分析
     * @access private
     * @param string $comment
     * @return string
     */
    protected function parseComment($comment)
    {
        return !empty($comment) ? ' /* ' . $comment . ' */' : '';
    }

    /**
     * distinct分析
     * @access private
     * @param mixed $distinct
     * @return string
     */
    protected function parseDistinct($distinct)
    {
        return !empty($distinct) ? ' DISTINCT ' : '';
    }

    /**
     * union分析
     * @access private
     * @param mixed $union
     * @return string
     */
    protected function parseUnion($union)
    {
        if (empty($union)) return '';
        if (isset($union['_all'])) {
            $str = 'UNION ALL ';
            unset($union['_all']);
        } else {
            $str = 'UNION ';
        }
        $sql = array();
        foreach ($union as $u) {
            $sql[] = $str . (is_array($u) ? $this->buildSelectSql($u) : $u);
        }
        return implode(' ', $sql);
    }

    /**
     * 设置锁机制
     * @access private
     * @param bool $lock
     * @return string
     */
    protected function parseLock($lock = false)
    {
        return $lock ? ' FOR UPDATE ' : '';
    }

    /**
     * index分析，可在操作链中指定需要强制使用的索引
     * @access private
     * @param mixed $index
     * @return string
     */
    protected function parseForce($index)
    {
        if (empty($index)) return '';
        if (is_array($index)) $index = join(",", $index);
        return sprintf(" FORCE INDEX ( %s ) ", $index);
    }

    /**
     * 生成查询SQL
     * @access private
     * @param array $options 表达式
     * @return string
     */
    protected function buildSelectSql($options = array())
    {
        if (isset($options['page'])) {
            // 根据页数计算limit
            list($page, $listRows) = $options['page'];
            $page = $page > 0 ? $page : 1;
            $listRows = $listRows > 0 ? $listRows : (is_numeric($options['limit']) ? $options['limit'] : 20);
            $offset = $listRows * ($page - 1);
            switch ($this->db_type) {
                case DBType::MSSQL:
                    $options['limit'] = $listRows;
                    $options['offset'] = $offset;
                    break;
                default:
                    $options['limit'] = $listRows . ' OFFSET ' . $offset;
                    break;
            }
        }
        $sql = $this->parseSql($this->selectSql, $options);
        return $sql;
    }

    /**
     * where分析
     * @access private
     * @param mixed $where
     * @return string
     */
    protected function parseWhere($where)
    {
        $whereStr = '';
        if ($this->where) {
            //闭包形式
            $whereStr = $this->builtWhereSql($this->where);
        } elseif ($where) {
            if (is_string($where)) {
                //直接字符串
                $whereStr = $where;
            } elseif (is_array($where)) {
                //数组形式,只支持field=>value形式 AND 逻辑 和 equalTo 条件
                $this->where = array();
                foreach ($where as $k => $v) {
                    $this->whereOperat(self::equalTo, $k, $v);
                }
                $whereStr = $this->builtWhereSql($this->where);
            }
        }
        return empty($whereStr) ? '' : ' WHERE ' . $whereStr;
    }

    /**
     * @param string $operat see self
     * @param string $field
     * @param null $value
     * @return self | Mysql | Pgsql | Mssql | Sqlite
     */
    protected function whereOperat($operat, $field, $value = null)
    {
        if ($operat == self::isNull || $operat == self::isNotNull || $value !== null) {//排除空值
            if ($operat != self::like || $operat != self::notLike || ($value != '%' && $value != '%%')) {//排除空like
                $this->where[] = array(
                    'operat' => $operat,
                    'table' => $this->where_table,
                    'field' => $field,
                    'value' => $value,
                );
            }
        }
        return $this;
    }

    /**
     * 构建where的SQL语句
     * @param $closure
     * @param string $sql
     * @param string $cond
     * @return string|null
     */
    private function builtWhereSql($closure, $sql = '', $cond = 'and')
    {
        foreach ($closure as $v) {
            $table = isset($v['table']) && $v['table'] ? $v['table'] : $this->getTable();
            if (!$table) {
                return null;
            }
            $ft = $this->getFieldType($table);
            if ($v['operat'] === 'closure') {
                $innerSql = '(' . $this->builtWhereSql($v['closure'], '', $v['cond']) . ')';
                $sql .= $sql ? " {$cond}{$innerSql} " : $innerSql;
            } else {
                $si = strpos($v['field'], '#>>');
                if ($si > 0) {
                    preg_match("/\(?(.*)#>>/", $v['field'], $siField);
                    $ft_type = $ft[$table . '_' . $siField[1]] ?? null;
                } else {
                    $ft_type = $ft[$table . '_' . $v['field']] ?? null;
                }
                if (empty($ft_type)) { // 根据表字段过滤无效field
                    continue;
                }
                if ($this->sqlFilter($v['value'])) {
                    $innerSql = ' ';
                    $field = $this->parseKey($v['field']);
                    if ($si > 0 && strpos($v['field'], '(') === 0) {
                        $innerSql .= '(' . $this->parseKey($table) . '.';
                        $innerSql .= substr($field, 1, strlen($field));
                    } else {
                        $innerSql .= $this->parseKey($table) . '.';
                        $innerSql .= $field;
                    }
                    $isContinue = false;
                    switch ($v['operat']) {
                        case self::equalTo:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " = {$value}";
                            break;
                        case self::notEqualTo:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " <> {$value}";
                            break;
                        case self::greaterThan:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " > {$value}";
                            break;
                        case self::greaterThanOrEqualTo:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " >= {$value}";
                            break;
                        case self::lessThan:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " < {$value}";
                            break;
                        case self::lessThanOrEqualTo:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " <= {$value}";
                            break;
                        case self::like:
                            if ($this->isUseCrypto()) {
                                $likeO = '';
                                $likeE = '';
                                $vspllit = str_split($v['value']);
                                if ($vspllit[0] === '%') {
                                    $likeO = array_shift($vspllit);
                                }
                                if ($vspllit[count($vspllit) - 1] === '%') {
                                    $likeE = array_pop($vspllit);
                                }
                                $value = $this->parseWhereByFieldType(implode('', $vspllit), $ft_type);
                                $value = $likeO . $value . $likeE;
                            } else {
                                $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            }
                            $value = $this->parseValue($value);
                            $innerSql .= " like {$value}";
                            break;
                        case self::notLike:
                            if (substr($ft_type, -2) === '[]') {
                                $innerSql = "array_to_string({$innerSql},'')";
                            }
                            if ($this->isUseCrypto()) {
                                $likeO = '';
                                $likeE = '';
                                $vspllit = str_split($v['value']);
                                if ($vspllit[0] === '%') {
                                    $likeO = array_shift($vspllit);
                                }
                                if ($vspllit[count($vspllit) - 1] === '%') {
                                    $likeE = array_pop($vspllit);
                                }
                                $value = $this->parseWhereByFieldType(implode('', $vspllit), $ft_type);
                                $value = $likeO . $value . $likeE;
                            } else {
                                $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            }
                            $value = $this->parseValue($value);
                            $innerSql .= " not like {$value}";
                            break;
                        case self::isNull:
                            $innerSql .= " is null ";
                            break;
                        case self::isNotNull:
                            $innerSql .= " is not null ";
                            break;
                        case self::between:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " between {$value[0]} and {$value[1]}";
                            break;
                        case self::notBetween:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $innerSql .= " not between {$value[0]} and {$value[1]}";
                            break;
                        case self::in:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $value = implode(',', (array)$value);
                            $innerSql .= " in ({$value})";
                            break;
                        case self::notIn:
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $value = implode(',', (array)$value);
                            $innerSql .= " not in ({$value})";
                            break;
                        case self::findInSetOr:
                            if ($this->db_type !== DBType::MYSQL) {
                                Exception::throw("{$v['operat']} not support {$this->db_type}");
                            }
                            if ($v['value']) {
                                $v['value'] = (array)$v['value'];
                                foreach ($v['value'] as $vfisk => $vfis) {
                                    if ($vfis) {
                                        $vfis = $this->parseWhereByFieldType($vfis, $ft_type);
                                        $vfis = $this->parseValue($vfis);
                                        if ($vfisk === 0) {
                                            $innerSql = " (find_in_set({$vfis},{$field})";
                                        } else {
                                            $innerSql .= " or find_in_set({$vfis},{$field})";
                                        }
                                    }
                                }
                                $innerSql .= ")";
                            } else {
                                $isContinue = true;
                            }
                            break;
                        case self::notFindInSetOr:
                            $this->askDBType(DBType::MYSQL, $v['operat']);
                            if ($v['value']) {
                                $v['value'] = (array)$v['value'];
                                foreach ($v['value'] as $vfisk => $vfis) {
                                    if ($vfis) {
                                        $vfis = $this->parseWhereByFieldType($vfis, $ft_type);
                                        $vfis = $this->parseValue($vfis);
                                        if ($vfisk === 0) {
                                            $innerSql = " (not find_in_set({$vfis},{$field})";
                                        } else {
                                            $innerSql .= " or not find_in_set({$vfis},{$field})";
                                        }
                                    }
                                }
                                $innerSql .= ")";
                            } else {
                                $isContinue = true;
                            }
                            break;
                        case self::findInSetAnd:
                            $this->askDBType(DBType::MYSQL, $v['operat']);
                            if ($v['value']) {
                                $v['value'] = (array)$v['value'];
                                foreach ($v['value'] as $vfisk => $vfis) {
                                    if ($vfis) {
                                        $vfis = $this->parseWhereByFieldType($vfis, $ft_type);
                                        $vfis = $this->parseValue($vfis);
                                        if ($vfisk === 0) {
                                            $innerSql = " (find_in_set({$vfis},{$field})";
                                        } else {
                                            $innerSql .= " and find_in_set({$vfis},{$field})";
                                        }
                                    }
                                }
                                $innerSql .= ")";
                            } else {
                                $isContinue = true;
                            }
                            break;
                        case self::notFindInSetAnd:
                            $this->askDBType(DBType::MYSQL, $v['operat']);
                            if ($v['value']) {
                                $v['value'] = (array)$v['value'];
                                foreach ($v['value'] as $vfisk => $vfis) {
                                    if ($vfis) {
                                        $vfis = $this->parseWhereByFieldType($vfis, $ft_type);
                                        $vfis = $this->parseValue($vfis);
                                        if ($vfisk === 0) {
                                            $innerSql = " (not find_in_set({$vfis},{$field})";
                                        } else {
                                            $innerSql .= " and not find_in_set({$vfis},{$field})";
                                        }
                                    }
                                }
                                $innerSql .= ")";
                            } else {
                                $isContinue = true;
                            }
                            break;
                        case self::any:
                            $this->askDBType(DBType::PGSQL, $v['operat']);
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->parseValue($value);
                            $value = (array)$value;
                            array_walk($value, function (&$value) {
                                $value = "({$value})";
                            });
                            $value = implode(',', $value);
                            $innerSql .= " = any (values {$value})";
                            break;
                        case self::contains:
                            $this->askDBType(DBType::PGSQL, $v['operat']);
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->toPGArray((array)$value, str_replace('[]', '', $ft_type));
                            $value = $this->parseValue($value);
                            $innerSql .= " @> {$value}";
                            break;
                        case self::isContainsBy:
                            $this->askDBType(DBType::PGSQL, $v['operat']);
                            $value = $this->parseWhereByFieldType($v['value'], $ft_type);
                            $value = $this->toPGArray((array)$value, str_replace('[]', '', $ft_type));
                            $value = $this->parseValue($value);
                            $innerSql .= " <@ {$value}";
                            break;
                        default:
                            $isContinue = true;
                            break;
                    }
                    if ($isContinue) continue;
                    $sql .= $sql ? " {$cond}{$innerSql} " : $innerSql;
                }
            }
        }
        return $sql;
    }

    /**
     * 清理where条件
     * @return $this
     */
    public function clearWhere()
    {
        $this->where = array();
        $this->where_table = '';
        return $this;
    }

    /**
     * 锁定为哪一个表的搜索条件
     * @param $table
     * @return $this
     */
    public function whereTable($table)
    {
        $this->where_table = $table;
        return $this;
    }

    /**
     * 替换SQL语句中表达式
     * @access private
     * @param string $sql
     * @param array $options 表达式
     * @return string
     */
    protected function parseSql($sql, $options = array())
    {
        switch ($this->db_type) {
            case DBType::MYSQL:
            case DBType::SQLITE:
                $sql = str_replace(
                    array('%TABLE%', '%ALIA%', '%DISTINCT%', '%FIELD%', '%JOIN%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%', '%UNION%', '%LOCK%', '%COMMENT%', '%FORCE%'),
                    array(
                        $this->parseTable(!empty($options['table_origin']) ? $options['table_origin'] : (isset($options['table']) ? $options['table'] : false)),
                        !empty($options['table_origin']) ? $this->parseTable(' AS ' . $options['table']) : null,
                        $this->parseDistinct(isset($options['distinct']) ? $options['distinct'] : false),
                        $this->parseField(!empty($options['field']) ? $options['field'] : '*'),
                        $this->parseJoin(!empty($options['join']) ? $options['join'] : ''),
                        $this->parseWhere(!empty($options['where']) ? $options['where'] : ''),
                        $this->parseGroupBy(!empty($options['group']) ? $options['group'] : ''),
                        $this->parseHaving(!empty($options['having']) ? $options['having'] : ''),
                        $this->parseOrderBy(!empty($options['order']) ? $options['order'] : ''),
                        $this->parseLimit(!empty($options['limit']) ? $options['limit'] : ''),
                        $this->parseUnion(!empty($options['union']) ? $options['union'] : ''),
                        $this->parseLock(isset($options['lock']) ? $options['lock'] : false),
                        $this->parseComment(!empty($options['comment']) ? $options['comment'] : ''),
                        $this->parseForce(!empty($options['force']) ? $options['force'] : '')
                    ), $sql);
                break;
            case DBType::PGSQL:
                $sql = str_replace(
                    array('%SCHEMAS%', '%TABLE%', '%ALIA%', '%DISTINCT%', '%FIELD%', '%JOIN%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%', '%UNION%', '%LOCK%', '%COMMENT%', '%FORCE%'),
                    array(
                        $this->parseSchemas(isset($options['schemas']) ? $options['schemas'] : false),
                        $this->parseTable(!empty($options['table_origin']) ? $options['table_origin'] : (isset($options['table']) ? $options['table'] : false)),
                        !empty($options['table_origin']) ? $this->parseTable(' AS ' . $options['table']) : null,
                        $this->parseDistinct(isset($options['distinct']) ? $options['distinct'] : false),
                        $this->parseField(!empty($options['field']) ? $options['field'] : '*'),
                        $this->parseJoin(!empty($options['join']) ? $options['join'] : ''),
                        $this->parseWhere(!empty($options['where']) ? $options['where'] : ''),
                        $this->parseGroupBy(!empty($options['group']) ? $options['group'] : ''),
                        $this->parseHaving(!empty($options['having']) ? $options['having'] : ''),
                        $this->parseOrderBy(!empty($options['order']) ? $options['order'] : ''),
                        $this->parseLimit(!empty($options['limit']) ? $options['limit'] : ''),
                        $this->parseUnion(!empty($options['union']) ? $options['union'] : ''),
                        $this->parseLock(isset($options['lock']) ? $options['lock'] : false),
                        $this->parseComment(!empty($options['comment']) ? $options['comment'] : ''),
                        $this->parseForce(!empty($options['force']) ? $options['force'] : '')
                    ), $sql);
                break;
            case DBType::MSSQL:
                $sql = str_replace(
                    array('%SCHEMAS%', '%TABLE%', '%ALIA%', '%DISTINCT%', '%FIELD%', '%JOIN%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%', '%OFFSET%', '%UNION%', '%LOCK%', '%COMMENT%', '%FORCE%'),
                    array(
                        $this->parseSchemas(isset($options['schemas']) ? $options['schemas'] : false),
                        $this->parseTable(!empty($options['table_origin']) ? $options['table_origin'] : (isset($options['table']) ? $options['table'] : false)),
                        !empty($options['table_origin']) ? $this->parseTable(' AS ' . $options['table']) : null,
                        $this->parseDistinct(isset($options['distinct']) ? $options['distinct'] : false),
                        $this->parseField(!empty($options['field']) ? $options['field'] : '*'),
                        $this->parseJoin(!empty($options['join']) ? $options['join'] : ''),
                        $this->parseWhere(!empty($options['where']) ? $options['where'] : ''),
                        $this->parseGroupBY(!empty($options['group']) ? $options['group'] : ''),
                        $this->parseHaving(!empty($options['having']) ? $options['having'] : ''),
                        $this->parseOrderBY(!empty($options['order']) ? $options['order'] : ''),
                        $this->parseLimit(!empty($options['limit']) ? $options['limit'] : ''),
                        $this->parseOffset(!empty($options['offset']) ? $options['offset'] : ''),
                        $this->parseUnion(!empty($options['union']) ? $options['union'] : ''),
                        $this->parseLock(isset($options['lock']) ? $options['lock'] : false),
                        $this->parseComment(!empty($options['comment']) ? $options['comment'] : ''),
                        $this->parseForce(!empty($options['force']) ? $options['force'] : '')
                    ), $sql);
                break;
            default:
                Exception::abort("ParseSql not support {$this->db_type} yet");
                break;
        }
        return $sql;
    }

    /**
     * 条件闭包
     * @param string $cond 'and' || 'or'
     * @param bool $isGlobal 'field or total'
     * @return self
     */
    public function closure(string $cond = 'and', bool $isGlobal = false)
    {
        if ($this->where) {
            $o = array();
            $f = array();
            foreach ($this->where as $v) {
                if ($v['operat'] === 'closure') {
                    $o[] = $v;
                } elseif ($v['field']) {
                    $f[] = $v;
                }
            }
            if ($o && $f) {
                if ($isGlobal === false) {
                    $this->where = $o;
                    $this->where[] = array('operat' => 'closure', 'cond' => $cond, 'closure' => $f);
                } else {
                    $this->where = array(array('operat' => 'closure', 'cond' => $cond, 'closure' => array_merge($o, $f)));
                }
            } elseif ($o && !$f) {
                $this->where = array(array('operat' => 'closure', 'cond' => $cond, 'closure' => $this->where));
            } elseif (!$o && $f) {
                $this->where = array(array('operat' => 'closure', 'cond' => $cond, 'closure' => $f));
            }
        }
        return $this;
    }

    /**
     * 设定为直接输出sql
     * @return self | Mysql | Pgsql | Mssql | Sqlite
     */
    public function fetchSql()
    {
        $this->fetchSql = true;
        return $this;
    }

    /**
     * 执行 SQL
     *
     * @param string $query
     * @param int $fetchMode
     * @return mixed
     */
    public function query($query = '', $fetchMode = PDO::FETCH_ASSOC)
    {
        $table = $this->getTable();
        if (!$table) {
            Exception::abort('lose table');
        }
        $query = trim($query);
        $this->lastSql = $query;
        if ($this->fetchSql === true) {
            return $this->lastSql;
        }

        $rawStatement = explode(" ", $query);
        $statement = strtolower(trim($rawStatement[0]));
        //read model,check cache
        if ($statement === 'select' || $statement === 'show') {
            $result = false;
            if ($this->auto_cache === AutoCache::FOREVER) {
                $result = Cache::uGet($table, $query);
            } elseif (is_numeric($this->auto_cache)) {
                $result = Cache::get($table . $query);
            }
            if ($result) return $result;
        }
        //释放前次的查询结果
        if (!$this->PDOStatement = $this->execute($query)) {
            Exception::abort($this->getError());
        }

        if ($statement === 'select' || $statement === 'show') {
            $result = $this->PDOStatement->fetchAll($fetchMode);
            $result = $this->fetchFormat($result);
            if ($this->auto_cache === AutoCache::FOREVER) {
                Cache::uSet($table, $query, $result);
            } elseif (is_numeric($this->auto_cache)) {
                Cache::set($table . $query, $result, (int)$this->auto_cache);
            }
            return $result;
        } elseif ($statement === 'update' || $statement === 'delete') {
            if ($this->auto_cache === 'forever') {
                Cache::clear($table);
            }
            return $this->PDOStatement->rowCount();
        } elseif ($statement === 'insert') {
            if ($this->auto_cache === AutoCache::FOREVER) {
                Cache::clear($table);
            }
            return $this->PDOStatement->rowCount();
        } else {
            return null;
        }
    }

    /**
     * 获取当前模式 schemas
     * @return string
     */
    protected function getSchemas()
    {
        return $this->options['schemas'];
    }

    /**
     * 获取当前table
     * @return string
     */
    protected function getTable()
    {
        return $this->options['table'] ?? null;
    }

    /**
     * 指定查询字段
     * @access protected
     * @param mixed $field
     * @param string | null $table
     * @param null $function
     * @return self
     */
    public function field($field, $table = null, $function = null)
    {
        if ($table === null) {
            $table = $this->getTable();
        }
        $tableLen = mb_strlen($table, 'utf-8');
        if (!$table) {
            return $this;
        }
        if (is_string($field)) {
            $field = explode(',', $field);
        }
        if (is_array($field)) {
            $field = array_filter($field);
            $ft = $this->getFieldType($table);
            $fk = array_keys($ft);
            $parseTable = $this->parseTable($table);
            foreach ($field as $k => $v) {
                $v = trim($v);
                if ($v === '*') {
                    unset($field[$k]);
                    foreach ($fk as $kk) {
                        if ($table === substr($kk, 0, $tableLen)) {
                            if (substr($ft[$kk], -2) === '[]') {
                                $field[] = "array_to_json({$parseTable}." . Str::replaceFirst("{$table}_", '', $kk) . ") as {$kk}";
                            } else {
                                $field[] = "{$parseTable}." . Str::replaceFirst("{$table}_", '', $kk) . " as {$kk}";
                            }
                        }
                    }
                } else {
                    $from = $v;
                    $to = $v;
                    $v = str_replace([' AS ', ' As ', ' => ', ' as '], ' as ', $v);
                    $asPos = strpos($v, ' as ');
                    if ($asPos > 0) {
                        $as = explode(' as ', $v);
                        $from = $as[0];
                        $to = $as[1];
                        $jsonPos = strpos($from, '#>>');
                        if ($jsonPos > 0) {
                            $jPos = explode('#>>', $v);
                            $ft[$table . '_' . $to] = $ft[$table . '_' . trim($jPos[0])];
                        } elseif (!empty($this->currentFieldType[$table . '_' . $from])) {
                            $this->currentFieldType[$table . '_' . $to] = $this->currentFieldType[$table . '_' . $from];
                            $ft[$table . '_' . $to] = $ft[$table . '_' . $from];
                        }
                    }

                    if (!isset($ft[$table . '_' . $to])) {
                        continue;
                    }
                    // check function
                    $tempParseTableForm = $parseTable . '.' . $from;
                    if ($function) {
                        if ($this->db_type === DBType::PGSQL) {
                            $func3 = strtoupper(substr($function, 0, 3));
                            switch ($func3) {
                                case 'SUM':
                                case 'AVG':
                                case 'MIN':
                                case 'MAX':
                                    $function = str_replace('%' . $k, "(%{$k})::numeric", $function);
                                    break;
                                default:
                                    break;
                            }
                        }
                        $tempParseTableForm = str_replace('%' . $k, $tempParseTableForm, $function);
                    }
                    if (strpos($ft[$table . '_' . $to], '[]') !== false) {
                        $field[$k] = "array_to_json({$tempParseTableForm}) as {$table}_{$to}";
                    } else {
                        $field[$k] = "{$tempParseTableForm} as {$table}_{$to}";
                    }
                }
            }
            if (!isset($this->options['field'])) {
                $this->options['field'] = array();
            }
            $this->options['field'] = array_merge_recursive($this->options['field'], $field);
        }
        return $this;
    }


}
