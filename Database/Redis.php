<?php

namespace PhpureCore\Database;

use Exception;
use PhpureCore\Glue\Response;
use PhpureCore\Mapping\DBType;
use Redis as RedisDriver;

class Redis extends AbstractDB
{

    protected $db_type = DBType::REDIS;

    const TYPE_FLAG = '|t|';

    const TYPE_JSON = 'json';
    const TYPE_STR = 'str';
    const TYPE_NUM = 'num';

    /**
     * @var RedisDriver | null
     *
     */
    private $redis = null;

    /**
     * 架构函数 取得模板对象实例
     * @access public
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        $this->host = $setting['host'] ?? null;
        $this->port = $setting['port'] ?? null;
        $this->password = $setting['password'] ?? '';
        if ($this->redis == null) {
            if (class_exists('\\Redis')) {
                try {
                    $this->redis = new RedisDriver();
                    $this->redis->connect(
                        $this->host,
                        $this->port
                    );
                    if ($this->password) {
                        $this->redis->auth($this->password);
                    }
                } catch (Exception $e) {
                    $this->redis = null;
                    Response::exception('Redis遇到问题或未安装，请停用Redis以减少阻塞卡顿');
                }
            }
        }
        return $this;
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
     * 最优化key
     * @param $key
     * @return string
     */
    private function tinyKey($key)
    {
        return gzdeflate($key);
    }

    /**
     * @param $key
     * @return string
     */
    private function parse($key)
    {
        return static::tinyKey($this->project_key . $key);
    }

    /**
     * 删除kEY
     * @param $key
     */
    public function delete($key)
    {
        if ($this->redis !== null && $key) {
            $this->redis->delete($this->parse($key));
        }
    }

    /**
     * 清空
     * @param bool $sure
     */
    public function flushAll($sure = false)
    {
        if ($this->redis !== null && $sure === true) {
            $this->redis->flushAll();
        }
    }

    /**
     * @param $key
     * @param $value
     * @param int $timeout <= 0 not expire
     * @return void
     */
    public function set($key, $value, int $timeout = 0)
    {
        if ($this->redis !== null && $key) {
            $key = $this->parse($key);
            if (is_array($value)) {
                $this->redis->set($key, json_encode($value));
                $this->redis->set($key . self::TYPE_FLAG, self::TYPE_JSON);
            } elseif (is_string($value)) {
                $this->redis->set($key, $value);
                $this->redis->set($key . self::TYPE_FLAG, self::TYPE_STR);
            } elseif (is_numeric($value)) {
                $this->redis->set($key, $value);
                $this->redis->set($key . self::TYPE_FLAG, self::TYPE_NUM);
            } else {
                $this->redis->set($key, $value);
            }
            if ($timeout > 0) {
                $this->redis->expire($key, $timeout);
            }
        }
    }

    /**
     * @param $key
     * @return bool|null|string|array
     */
    public function get($key)
    {
        if ($this->redis === null || !$key) {
            return null;
        } else {
            $key = $this->parse($key);
            $type = $this->redis->get($key . self::TYPE_FLAG);
            switch ($type) {
                case self::TYPE_JSON:
                    $result = json_decode($this->redis->get($key), true);
                    break;
                case self::TYPE_STR:
                    $result = $this->redis->get($key);
                    break;
                case self::TYPE_NUM:
                    $result = round($this->redis->get($key), 10);
                    break;
                default:
                    $result = $this->redis->get($key);
                    break;
            }
            return $result;
        }
    }

    /**
     * @param $table
     * @param $key
     * @param $value
     * @return void
     */
    public function hSet($table, $key, $value)
    {
        if ($this->redis !== null && $table && $key) {
            $table = $this->parse($table);
            if (is_array($value)) {
                $this->redis->hSet($table, $key, json_encode($value));
                $this->redis->hSet($table, $key . self::TYPE_FLAG, self::TYPE_JSON);
            } elseif (is_string($value)) {
                $this->redis->hSet($table, $key, $value);
                $this->redis->hSet($table, $key . self::TYPE_FLAG, self::TYPE_STR);
            } elseif (is_numeric($value)) {
                $this->redis->hSet($table, $key, $value);
                $this->redis->hSet($table, $key . self::TYPE_FLAG, self::TYPE_NUM);
            } else {
                $this->redis->hSet($table, $key, $value);
            }
        }
    }

    /**
     * @param $table
     * @param $key
     * @return bool|null|string|array
     */
    public function hGet($table, $key)
    {
        if ($this->redis === null || !$table || !$key) {
            return null;
        } else {
            $table = $this->parse($table);
            $type = $this->redis->hGet($table, $key . self::TYPE_FLAG);
            switch ($type) {
                case self::TYPE_JSON:
                    $result = json_decode($this->redis->hGet($table, $key), true);
                    break;
                case self::TYPE_STR:
                    $result = (string)$this->redis->hGet($table, $key);
                    break;
                case self::TYPE_NUM:
                    $result = round($this->redis->hGet($table, $key), 10);
                    break;
                default:
                    $result = $this->redis->hGet($table, $key);
                    break;
            }
            return $result;
        }
    }

    /**
     * @param $key
     * @return void
     */
    public function incr($key)
    {
        if ($this->redis !== null && $key) {
            $key = $this->parse($key);
            $this->redis->incr($key);
        }
    }

    /**
     * @param $key
     * @return void
     */
    public function decr($key)
    {
        if ($this->redis !== null && $key) {
            $key = $this->parse($key);
            $this->redis->decr($key);
        }
    }

}