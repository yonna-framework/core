<?php
/**
 * 数据库连接类，依赖 PDO_SQLSRV 扩展
 * version >= 2012
 */

namespace PhpureCore\Database;

use Exception;
use PDOException;
use PhpureCore\Mapping\DBType;

class Mssql extends AbstractPDO
{

    /**
     * 构造方法
     *
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        $this->host = $setting['host'];
        $this->port = $setting['port'];
        $this->account = $setting['account'];
        $this->password = $setting['password'];
        $this->name = $setting['name'];
        $this->charset = $setting['charset'] ?: 'gbk';
        $this->auto_cache = $setting['auto_cache'];

        $this->db_type = DBType::MSSQL;
        $this->selectSql = 'SELECT%LIMIT%%DISTINCT% %FIELD% FROM %SCHEMAS%.%TABLE% %ALIA% %FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%OFFSET%%UNION%%LOCK%%COMMENT%';
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        parent::__destruct();
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
        return $sql;
    }



    /**
     * 开始事务
     */
    public function beginTrans()
    {
        if ($this->_transTrace <= 0) {
            if ($this->pdo()->inTransaction()) {
                $this->pdo()->commit();
            }
            $this->_transTrace = 1;
        } else {
            $this->_transTrace++;
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
        $this->_transTrace > 0 && $this->_transTrace--;
        if ($this->_transTrace > 0) {
            return true;
        }
        return $this->pdo()->commit();
    }

    /**
     * 事务回滚
     */
    public function rollBackTrans()
    {
        $this->_transTrace > 0 && $this->_transTrace--;
        if ($this->_transTrace > 0) {
            return true;
        }
        if ($this->pdo()->inTransaction()) {
            return $this->pdo()->rollBack();
        }
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
     * 获取当前模式 schemas
     * @return string
     */
    protected function getSchemas()
    {
        return $this->options['schemas'];
    }

    /**
     * 哪个模式
     *
     * @param string $schemas
     * @return self
     */
    public function schemas($schemas)
    {
        $this->resetAll();
        $this->options['schemas'] = $schemas;
        return $this;
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
     * 哪个表
     *
     * @param string $table
     * @return self
     */
    public function table($table)
    {
        $table = str_replace([' as ', ' AS ', ' As ', ' aS ', ' => '], ' ', trim($table));
        $tableEX = explode(' ', $table);
        if (count($tableEX) === 2) {
            $this->options['table'] = $tableEX[1];
            $this->options['table_origin'] = $tableEX[0];
            if (!isset($this->options['alia'])) {
                $this->options['alia'] = array();
            }
            $this->options['alia'][$tableEX[1]] = $tableEX[0];
        } else {
            $this->options['table'] = $table;
            $this->options['table_origin'] = null;
        }
        return $this;
    }

    /**
     * USING支持 用于多表删除
     * @access public
     * @param mixed $using
     * @return self
     */
    public function using($using)
    {
        if ($using) {
            $this->options['using'] = $using;
        }
        return $this;
    }

    /**
     * 查询SQL组装 union
     * @access public
     * @param mixed $union
     * @param boolean $all
     * @return self
     */
    public function union($union, $all = false)
    {
        if (empty($union)) return $this;
        if ($all) {
            $this->options['union']['_all'] = true;
        }
        if (is_object($union)) {
            $union = get_object_vars($union);
        }
        // 转换union表达式
        $options = null;
        if (is_array($union)) {
            if (isset($union[0])) {
                $this->options['union'] = array_merge($this->options['union'], $union);
                return $this;
            } else {
                $this->options['union'][] = $union;
            }
        } elseif (is_string($options)) {
            $this->options['union'][] = $options;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getJoinQty()
    {
        return (int)$this->options['joinQty'];
    }

    /**
     * 查询SQL组装 join
     * @access public
     * @param mixed $join
     * @param string $type JOIN类型
     * @return self
     */
    private function joinTo($join, $type = 'LEFT')
    {
        if (is_array($join)) {
            foreach ($join as $key => &$_join) {
                $_join = false !== stripos($_join, 'JOIN') ? $_join : $type . ' JOIN ' . $_join;
            }
            $this->options['join'] = $join;
        } elseif (!empty($join)) {
            $this->options['join'][] = false !== stripos($join, 'JOIN') ? $join : $type . ' JOIN ' . $join;
        }
        return $this;
    }

    /**
     * @param $target
     * @param $join
     * @param array $req
     * @param string $type INNER | OUTER | LEFT | RIGHT
     * @return self
     */
    public function join($target, $join, $req = array(), $type = 'INNER')
    {
        if ($target && $join) {
            $join = str_replace([' as ', ' AS ', ' As ', ' aS ', ' => '], ' ', trim($join));
            $originJoin = $join = explode(' ', $join);
            $alia = null;
            if (isset($join[1]) && $join[1]) {
                $alia = $this->parseKey($join[1]);
            }
            if (isset($join[0]) && $join[0]) {
                $join = $this->parseKey($join[0]);
            }
            $target = $this->parseKey($target);
            $join = $this->parseKey($join);
            $jsonStr = $join;
            $jsonStr .= $alia ? " AS {$alia}" : "";
            if ($req) {
                $jsonStr .= ' ON ';
                $first = false;
                foreach ($req as $k => $v) {
                    if (!$first) {
                        $first = true;
                        $jsonStr .= $alia ? "{$target}.{$k}={$alia}.{$v}" : "{$target}.{$k}={$join}.{$v}";
                    } else $jsonStr .= " AND " . ($alia ? "{$target}.{$k}={$alia}.{$v}" : '');
                }
            }
            if (!isset($this->options['joinQty'])) {
                $this->options['joinQty'] = 0;
            }
            $this->options['joinQty']++;
            if ($alia) {
                if (!isset($this->options['alia'])) {
                    $this->options['alia'] = array();
                }
                $this->options['alia'][$originJoin[1]] = $originJoin[0];
            }
            $this->joinTo($jsonStr, $type);
        }
        return $this;
    }


    public function clearWhere()
    {
        $this->where = array();
        $this->where_table = '';
        return $this;
    }

    public function whereTable($table)
    {
        $this->where_table = $table;
        return $this;
    }

    /**
     * 条件闭包
     * @param string $cond 'and' || 'or'
     * @param boolean $isGlobal 'field or total'
     * @return self
     */
    public function closure($cond = 'and', $isGlobal = false)
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
     * @param $field
     * @param $value
     * @return self
     */
    public function equalTo($field, $value)
    {
        return $this->whereOperat(self::equalTo, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notEqualTo($field, $value)
    {
        return $this->whereOperat(self::notEqualTo, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function greaterThan($field, $value)
    {
        return $this->whereOperat(self::greaterThan, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function greaterThanOrEqualTo($field, $value)
    {
        return $this->whereOperat(self::greaterThanOrEqualTo, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function lessThan($field, $value)
    {
        return $this->whereOperat(self::lessThan, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function lessThanOrEqualTo($field, $value)
    {
        return $this->whereOperat(self::lessThanOrEqualTo, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function like($field, $value)
    {
        return $this->whereOperat(self::like, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notLike($field, $value)
    {
        return $this->whereOperat(self::notLike, $field, $value);
    }

    /**
     * @param $field
     * @return self
     */
    public function isNull($field)
    {
        return $this->whereOperat(self::isNull, $field);
    }

    /**
     * @param $field
     * @return self
     */
    public function isNotNull($field)
    {
        return $this->whereOperat(self::isNotNull, $field);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function between($field, $value)
    {
        if (is_string($value)) $value = explode(',', $value);
        if (!is_array($value)) $value = (array)$value;
        if (count($value) !== 2) return $this;
        if (!$value[0]) return $this;
        if (!$value[1]) return $this;
        return $this->whereOperat(self::between, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notBetween($field, $value)
    {
        if (is_string($value)) $value = explode(',', $value);
        if (!is_array($value)) $value = (array)$value;
        if (count($value) !== 2) return $this;
        return $this->whereOperat(self::notBetween, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function in($field, $value)
    {
        return $this->whereOperat(self::in, $field, $value);
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function notIn($field, $value)
    {
        return $this->whereOperat(self::notIn, $field, $value);
    }

    /**
     * @param array $where
     * @return self
     */
    public function where(array $where)
    {
        if ($where) {
            foreach ($where as $k => $v) {
                $this->equalTo($k, $v);
            }
        }
        return $this;
    }

    /**
     * json闭包器
     * @param $string
     * @param null $closure
     * @return array|null
     */
    private function jsonClosure($string = null, $closure = null)
    {
        if ($closure === null) {
            $closure = array();
        }
        if (mb_strlen($string, 'utf-8') > 0) {
            $end = mb_strpos($string, ')', 0, 'utf-8');
            if ($end === false) $end = mb_strlen($string, 'utf-8');
            $start = mb_strripos(mb_substr($string, 0, $end, 'utf-8'), '(', 0, 'utf-8');
            if ($start === false) $start = 0;

            $isOver = false;
            if ($start === 0 && $end === mb_strlen($string, 'utf-8')) {
                $isOver = true;
            }
            $res = mb_substr($string, $isOver ? $start : $start + 1, $isOver ? $end : $end - $start - 1, 'utf-8');
            $and = explode('&&', $res);
            $or = explode('||', $res);
            $an = substr_count($res, '&&');
            $on = substr_count($res, '||');

            $s = str_replace("({$res})", '', $string);
            $sn = substr_count($s, '( &&') + substr_count($s, '&& )') + substr_count($s, '|| )') + substr_count($s, '( ||');

            if ($an < $on) {
                $cond = 'or';
                $conds = $or;
            } else {
                $cond = 'and';
                $conds = $and;
            }
            $cl = array();
            foreach ($conds as $ds) {
                $ds = trim($ds);
                if ($ds) {
                    $cl[] = $ds;
                }
            }
            $closure[] = array(
                'cond' => $cond,
                'fields' => $cl,
            );
            if ($sn === 0) {
                $closure = array(
                    array(
                        'cond' => $cond,
                        'fields' => $closure,
                    )
                );
            }
            //todo
            if ($isOver) {
                return $closure;
            }
            $closure = $this->jsonClosure($s, $closure);
        }
        return $closure;
    }

    /**
     * json选择器
     * @param $field
     * @param $array
     * @return self
     */
    private function jsonWhere($field, $array)
    {
        if (!is_array($array)) return $this;
        foreach ($array as $v) {
            if (!empty($v['fields']) && is_array($v['fields'])) {
                $this->jsonWhere($field, $v['fields']);
                ($v['cond']) && $this->closure($v['cond']);
            } elseif (is_string($v)) {
                preg_match("/{(.*)} (.*) #(.*)/i", $v, $match);
                if (!$match) {
                    continue;
                }
                $tempMatch1 = explode(',', $match[1]);
                $matchField = '$';
                foreach ($tempMatch1 as $tmk => $tm1) {
                    if (is_numeric($tm1)) {
                        $matchField .= "[{$tm1}]";
                    } elseif (is_string($tm1)) {
                        $matchField .= ".{$tm1}";
                    } else {
                        exit('jsonWhere error');
                    }
                }
                $matchField = "\"{$matchField}\"";
                $matchField = $field . '->' . $matchField;
                $matchOperat = $match[2] ?? null;
                $matchValue = $match[3] ?? null;
                if ($matchField === null) continue;
                if ($matchOperat === null) continue;
                switch ($matchOperat) {
                    case 'n':
                        $this->isNull($matchField);
                        break;
                    case '!n':
                        $this->isNotNull($matchField);
                        break;
                    case '=':
                        $this->equalTo($matchField, $matchValue);
                        break;
                    case '<>':
                    case '!=':
                        $this->notEqualTo($matchField, $matchValue);
                        break;
                    case '%':
                        $matchField = "({$matchField})::text";
                        $this->like($matchField, $matchValue);
                        break;
                    case '!%':
                        $matchField = "({$matchField})::text";
                        $this->notLike($matchField, $matchValue);
                        break;
                    case '>':
                        $matchField = "({$matchField})::numeric";
                        $this->greaterThan($matchField, $matchValue);
                        break;
                    case '>=':
                        $matchField = "({$matchField})::numeric";
                        $this->greaterThanOrEqualTo($matchField, $matchValue);
                        break;
                    case '<':
                        $matchField = "({$matchField})::numeric";
                        $this->lessThan($matchField, $matchValue);
                        break;
                    case '<=':
                        $matchField = "({$matchField})::numeric";
                        $this->lessThanOrEqualTo($matchField, $matchValue);
                        break;
                    case '><':
                        $this->between($matchField, explode(',', $matchValue));
                        break;
                    case '!><':
                        $this->notBetween($matchField, explode(',', $matchValue));
                        break;
                    case '^':
                        $this->in($matchField, explode(',', $matchValue));
                        break;
                    case '!^':
                        $this->notIn($matchField, explode(',', $matchValue));
                        break;
                    default:
                        continue;
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * @param $field
     * @param $value
     * @return self
     */
    public function json($field, $value)
    {

        if (is_string($value)) {
            $value = $this->jsonClosure($value);
        }
        $this->jsonWhere($field, $value);
        return $this;
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
                            $field[] = "{$parseTable}." . mb_str_replace_once("{$table}_", '', $kk) . " as {$kk}";
                        }
                    }
                } else {
                    $from = $v;
                    $to = $v;
                    $v = str_replace([' AS ', ' As ', ' => ', ' as '], ' as ', $v);
                    $aspos = strpos($v, ' as ');
                    if ($aspos > 0) {
                        $as = explode(' as ', $v);
                        $from = $as[0];
                        $to = $as[1];
                        $jsonPos = strpos($from, '#>>');
                        if ($jsonPos > 0) {
                            $jpos = explode('#>>', $v);
                            $ft[$table . '_' . $to] = $ft[$table . '_' . trim($jpos[0])];
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
                        if (substr($function, 0, 3) === 'SUM') {
                            $function = str_replace('%' . $k, "(%{$k})::numeric", $function);
                        }
                        $tempParseTableForm = str_replace('%' . $k, $tempParseTableForm, $function);
                    }
                    $field[$k] = "{$tempParseTableForm} as {$table}_{$to}";
                }
            }
            if (!isset($this->options['field'])) {
                $this->options['field'] = array();
            }
            $this->options['field'] = array_merge_recursive($this->options['field'], $field);
        }
        return $this;
    }

    /**
     * group by
     * @access protected
     * @param mixed $groupBy
     * @param string | null $table
     * @return self
     */
    public function groupBy($groupBy, $table = null)
    {
        if (is_array($groupBy)) {
            $groupBy = implode(',', $groupBy);
        }
        if (!is_string($groupBy)) {
            return $this;
        }
        if (!isset($this->options['group'])) {
            $this->options['group'] = '';
        }
        if ($this->options['group'] != '') {
            $this->options['group'] .= ',';
        }
        if ($table) {
            $this->options['group'] .= $this->parseTable($table) . '.' . $groupBy;
        } else $this->options['group'] .= $groupBy;
        return $this;
    }

    /**
     * order by
     * @access protected
     * @param mixed $orderBy 支持格式 'uid asc' | array('uid asc','pid desc')
     * @param string $sort
     * @param string | null $table
     * @return self
     */
    public function orderBy($orderBy, $sort = self::ASC, $table = null)
    {
        if (!$orderBy) {
            return $this;
        }
        if (!isset($this->options['order'])) {
            $this->options['order'] = array();
        }
        if ($table) {
            $table = $this->parseTable($table);
        }
        if (is_string($orderBy)) {
            $sort = strtolower($sort);
            if ($table) {
                $this->options['order'][$table . '.' . $orderBy] = $sort;
            } else {
                $this->options['order'][$orderBy] = $sort;
            }
        } elseif (is_array($orderBy)) {
            $orderBy = array_filter($orderBy);
            foreach ($orderBy as $v) {
                $orderInfo = explode(' ', $v);
                $orderInfo[1] = strtolower($orderInfo[1]);
                if ($table) {
                    $this->options['order'][$table . '.' . $orderInfo[0]] = $orderInfo[1];
                } else {
                    $this->options['order'][$orderInfo[0]] = $orderInfo[1];
                }
                unset($orderInfo);
            }
        }
        return $this;
    }

    /**
     * order by string 支持 field asc | field desc 形式
     * @param $orderBy
     * @param null $table
     * @return self
     */
    public function orderByStr($orderBy, $table = null)
    {
        $orderBy = explode(',', $orderBy);
        foreach ($orderBy as $o) {
            $o = explode(' ', $o);
            if ($table) {
                $this->options['order'][$table . '.' . $o[0]] = $o[1];
            } else {
                $this->options['order'][$o[0]] = $o[1];
            }
        }
        return $this;
    }

    /**
     * having
     * @access protected
     * @param mixed $having
     * @param string | null $table
     * @return self
     */
    public function having($having, $table = null)
    {
        if (!is_string($having)) {
            return $this;
        }
        if (!isset($this->options['having'])) {
            $this->options['having'] = '';
        }
        if ($this->options['having'] != '') {
            $this->options['having'] .= ',';
        }
        if ($table) {
            $this->options['having'] .= $this->$having($table) . '.' . $having;
        } else {
            $this->options['having'] .= $having;
        }
        return $this;
    }

    /**
     * 指定查询数量
     * @access protected
     * @param mixed $offset 起始位置
     * @param mixed $length 查询数量
     * @return self
     */
    public function limit($offset, $length = null)
    {
        if (is_null($length) && strpos($offset, ',')) {
            list($offset, $length) = explode(',', $offset);
        }
        if ($length === null) {
            $this->options['limit'] = $offset;
            $this->options['offset'] = null;
        } else {
            $this->options['limit'] = $length;
            $this->options['offset'] = $offset;
        }
        return $this;
    }

    ///TODO 终结操作

    /**
     * 查找记录多条
     * @access public
     * @return mixed
     */
    public function multi()
    {
        $options = $this->_parseOptions();
        $sql = $this->buildSelectSql($options);
        return $this->query($sql);
    }

    /**
     * 查找记录一条
     * @return mixed
     */
    public function one()
    {
        $this->limit(1);
        $result = $this->multi();
        return is_array($result) ? reset($result) : array();
    }

    /**
     * 当前时间（只能用于insert 和 update）
     * @return array
     */
    public function now()
    {
        return array('exp', 'GETDATE()');
    }

    /**
     * 统计
     * @param int $field
     * @return int
     */
    public function count($field = 0)
    {
        $this->field("COUNT(" . ($field === 0 ? '0' : $this->parseKey($field)) . ") AS \"hcount\"");
        $result = $this->one();
        return (int)$result['hcount'];
    }

    /**
     * 求和
     * @param string $field
     * @return int
     */
    public function sum($field)
    {
        $this->field("SUM(" . $this->parseKey($field) . ") AS \"hsum\"");
        $result = $this->one();
        return round($result['hsum'], 10);
    }

    /**
     * 求均
     * @param $field
     * @return int
     */
    public function avg($field)
    {
        $this->field("AVG(" . $this->parseKey($field) . ") AS \"havg\"");
        $result = $this->one();
        return round($result['havg'], 10);
    }

    /**
     * 求最小
     * @param $field
     * @return int
     */
    public function min($field)
    {
        $this->field("MIN(" . $this->parseKey($field) . ") AS \"hmin\"");
        $result = $this->one();
        return round($result['hmin'], 10);
    }

    /**
     * 求最大
     * @param $field
     * @return int
     */
    public function max($field)
    {
        $this->field("MAX(" . $this->parseKey($field) . ") AS \"hmax\"");
        $result = $this->one();
        return round($result['hmax'], 10);
    }

    /**
     * 分页查找
     * @param int $current
     * @param int $per
     * @return mixed
     */
    public function page($current = 0, $per = 10)
    {
        $limit = (int)$per;
        $offset = (int)($current) * $limit;
        $this->limit($offset, $limit);

        $options = $this->_parseOptions();
        $sql = $this->buildSelectSql($options);
        $options['order'] = null;
        $options['limit'] = 1;
        $options['offset'] = 0;
        if (!empty($options['group'])) {
            $options['field'] = 'count(DISTINCT ' . $options['group'] . ') as "hcount"';
            $options['group'] = null;
        } else {
            $options['field'] = 'count(0) as "hcount"';
        }
        $sqlCount = $this->buildSelectSql($options);
        $data = $this->query($sql);
        $count = $this->query($sqlCount);
        $count = reset($count)['hcount'];
        $count = (int)$count;
        //
        $result = array();
        $per = !$per ? 10 : $per;
        $end = ceil($count / $per);
        $result['data'] = $data;
        $result['page'] = [];
        $result['page']['total'] = $count;
        $result['page']['current'] = (int)$current;
        $result['page']['per'] = $per;
        $result['page']['end'] = (int)$end;
        return $result;
    }

    /**
     * 插入记录
     * @access public
     * @param mixed $data 数据
     * @return integer
     * @throws Exception
     */
    public function insert($data)
    {
        $values = $fields = array();
        $table = $this->getTable();
        $ft = $this->getFieldType($table);
        foreach ($data as $key => $val) {
            if (!empty($ft[$table . '_' . $key])) { // 根据表字段过滤无效key
                if (is_array($val) && isset($val[0]) && 'exp' == $val[0]) {
                    $fields[] = $this->parseKey($key);
                    $values[] = $val[1] ?? null;
                } elseif (is_null($val)) {
                    $fields[] = $this->parseKey($key);
                    $values[] = 'NULL';
                } elseif (is_array($val) || is_scalar($val)) { // 过滤非标量数据
                    //todo 跟据表字段处理数据
                    $val = $this->parseValueByFieldType($val, $ft[$table . '_' . $key]);
                    if ($val !== null) {
                        $fields[] = $this->parseKey($key);
                        $values[] = $this->parseValue($val);
                    }
                }
            }
        }
        // 兼容数字传入方式
        $sql = 'INSERT INTO ' . $this->parseTable($table) . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
        return $this->query($sql);
    }

    /**
     * 批量插入记录
     * @access public
     * @param mixed $dataSet 数据集
     * @return false | integer
     * @throws Exception
     */
    public function insertAll($dataSet)
    {
        $values = array();
        if (!is_array($dataSet[0])) return false;
        $fields = array_map(array($this, 'parseKey'), array_keys($dataSet[0]));
        $table = $this->getTable();
        $ft = $this->getFieldType($table);
        foreach ($dataSet as $data) {
            $value = array();
            foreach ($data as $key => $val) {
                if (!empty($ft[$table . '_' . $key])) { // 根据表字段过滤无效key
                    if (is_array($val) && isset($val[0]) && 'exp' == $val[0]) {
                        $value[] = $val[1];
                    } elseif (is_null($val)) {
                        $value[] = 'NULL';
                    } elseif (is_array($val) || is_scalar($val)) { // 过滤非标量数据
                        //todo 跟据表字段处理数据
                        $val = $this->parseValueByFieldType($val, $ft[$table . '_' . $key]);
                        if ($val !== null) {
                            $value[] = $this->parseValue($val);
                        }
                    }
                }
            }
            $values[] = '(' . implode(',', $value) . ')';
        }
        $sql = 'INSERT INTO ' . $this->parseTable($table) . ' (' . implode(',', $fields) . ') VALUES ' . implode(' , ', $values);
        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
        return $this->query($sql);
    }

    /**
     * 更新记录
     * @access public
     * @param mixed $data 数据
     * @param bool $sure
     * @return false | integer
     * @throws Exception
     */
    public function update($data, $sure = false)
    {
        $table = $this->getTable();
        $sql = 'UPDATE ' . $this->parseTable($table);
        $ft = $this->getFieldType($table);
        $set = array();
        foreach ($data as $key => $val) {
            if (!empty($ft[$table . '_' . $key])) { // 根据表字段过滤无效key
                if (is_array($val) && !empty($val[0]) && 'exp' == $val[0]) {
                    $set[] = $this->parseKey($key) . '=' . $val[1];
                } elseif (is_null($val)) {
                    $set[] = $this->parseKey($key) . '= NULL';
                } elseif (is_array($val) || is_scalar($val)) { // 过滤非标量数据
                    //todo 跟据表字段处理数据
                    $val = $this->parseValueByFieldType($val, $ft[$table . '_' . $key]);
                    if ($val !== null) {
                        $set[] = $this->parseKey($key) . '=' . $this->parseValue($val);
                    }
                }
            }
        }
        $sql .= ' SET ' . implode(',', $set);
        if (strpos($table, ',')) {// 多表更新支持JOIN操作
            $sql .= $this->parseJoin(!empty($this->options['join']) ? $this->options['join'] : '');
        }
        $where = $this->parseWhere(!empty($this->options['where']) ? $this->options['where'] : '');
        if (!$where && $sure !== true) {
            throw new \Exception('update must be sure when without where：' . $sql);
        }
        $sql .= $where;
        if (!strpos($table, ',')) {
            //  单表更新支持order和limit
            $sql .= $this->parseOrder(!empty($this->options['order']) ? $this->options['order'] : '')
                . $this->parseLimit(!empty($this->options['limit']) ? $this->options['limit'] : '');
        }
        $sql .= $this->parseComment(!empty($this->options['comment']) ? $this->options['comment'] : '');
        return $this->query($sql);
    }

    /**
     * 删除记录
     * @access public
     * @param bool $sure
     * @return false | integer
     * @throws Exception
     */
    public function delete($sure = false)
    {
        $table = $this->parseTable($this->options['table']);
        $sql = 'DELETE FROM ' . $table;
        if (strpos($table, ',')) {// 多表删除支持USING和JOIN操作
            if (!empty($this->options['using'])) {
                $sql .= ' USING ' . $this->parseTable($this->options['using']) . ' ';
            }
            $sql .= $this->parseJoin(!empty($this->options['join']) ? $this->options['join'] : '');
        }
        $where = $this->parseWhere(!empty($this->options['where']) ? $this->options['where'] : '');
        if (!$where && $sure !== true) {
            throw new \Exception('delete must be sure when without where');
        }
        $sql .= $where;
        if (!strpos($table, ',')) {
            // 单表删除支持order和limit
            $sql .= $this->parseOrder(!empty($this->options['order']) ? $this->options['order'] : '')
                . $this->parseLimit(!empty($this->options['limit']) ? $this->options['limit'] : '');
        }
        $sql .= $this->parseComment(!empty($this->options['comment']) ? $this->options['comment'] : '');
        return $this->query($sql);
    }

    /**
     * 截断表
     * @alert 必须注意，这个方法一经执行会“清空”原来的“所有数据”及“自增量”
     * @param bool $sure 确认执行，防止误操作
     * @return self
     * @throws Exception
     */
    public function truncate($sure = false)
    {
        if ($this->getTable() && $sure === true) {
            $sqlStr = "TRUNCATE TABLE " . $this->parseTable($this->getTable());
            return $this->query($sqlStr);
        }
        return $this;
    }

}
