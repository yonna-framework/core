<?php

namespace Yonna\Database;

use Yonna\Exception\Exception;
use Yonna\Mapping\DBType;
use Redis as RedisDriver;
use Swoole\Coroutine\Redis as RedisSwoole;

class Redis extends AbstractDB
{

    protected $db_type = DBType::REDIS;


    const TYPE_OBJ = 'o';
    const TYPE_STR = 's';
    const TYPE_NUM = 'n';

    /**
     * @var RedisDriver | RedisSwoole | null
     *
     */
    private $redis = null;

    /**
     * 架构函数 取得模板对象实例
     * @access public
     * @param array $setting
     * @param RedisSwoole | null $RedisDriver
     */
    public function __construct(array $setting, $RedisDriver = null)
    {
        parent::__construct($setting);
        if ($RedisDriver == null) {
            if (class_exists('\\Redis')) {
                try {
                    $RedisDriver = new RedisDriver();
                } catch (\Exception $e) {
                    $this->redis = null;
                    Exception::throw('Redis遇到问题或未安装，请暂时停用Redis以减少阻塞卡顿');
                }
            }
        }
        $this->redis = $RedisDriver;
        $this->redis->connect(
            $this->host,
            $this->port
        );
        if ($this->password) {
            $this->redis->auth($this->password);
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
    protected function tinyKey($key)
    {
        return gzdeflate($key);
    }

    /**
     * @param $key
     * @return string
     */
    protected function parse($key)
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
     * @return int
     */
    public function dbSize()
    {
        $size = -1;
        if ($this->redis !== null) {
            $size = $this->redis->dbSize();
        }
        return $size;
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
                $this->redis->set($key, self::TYPE_OBJ . json_encode($value));
            } elseif (is_string($value)) {
                $this->redis->set($key, self::TYPE_STR . $value);
            } elseif (is_numeric($value)) {
                $this->redis->set($key, self::TYPE_NUM . $value);
            } else {
                $this->redis->set($key, self::TYPE_STR . $value);
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
            $value = $this->redis->get($key);
            $type = substr($value, 0, 1);
            $value = substr($value, 1);
            switch ($type) {
                case self::TYPE_OBJ:
                    $value = json_decode($value, true);
                    break;
                case self::TYPE_NUM:
                    $value = round($value, 10);
                    break;
                case self::TYPE_STR:
                default:
                    break;
            }
            return $value;
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
                $this->redis->hSet($table, self::TYPE_OBJ . $key, json_encode($value));
            } elseif (is_string($value)) {
                $this->redis->hSet($table, self::TYPE_STR . $key, $value);
            } elseif (is_numeric($value)) {
                $this->redis->hSet($table, self::TYPE_NUM . $key, $value);
            } else {
                $this->redis->hSet($table, self::TYPE_STR . $key, $value);
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
            $value = $this->redis->hGet($table, $key);
            $type = substr($value, 0, 1);
            $value = substr($value, 1);
            switch ($type) {
                case self::TYPE_OBJ:
                    $value = json_decode($value, true);
                    break;
                case self::TYPE_NUM:
                    $value = round($value, 10);
                    break;
                case self::TYPE_STR:
                default:
                    break;
            }
            return $value;
        }
    }

    /**
     * @param $key
     * @param int $value
     * @return int | float
     */
    public function incr($key, $value = 1)
    {
        $answer = -1;
        if ($this->redis === null || !$key) {
            return $answer;
        }
        $key = $this->parse($key);
        if ($value === 1) {
            $answer = $this->redis->incr($key);
        } else {
            $answer = is_int($value) ? $this->redis->incrBy($key, $value) : $this->redis->incrByFloat($key, $value);
        }
        return $answer;
    }

    /**W
     * @param $key
     * @param int $value
     * @return int
     */
    public function decr($key, $value = 1)
    {
        $answer = -1;
        if ($this->redis === null || !$key) {
            return $answer;
        }
        $key = $this->parse($key);
        if ($value === 1) {
            $answer = $this->redis->decr($key);
        } else {
            $answer = $this->redis->decrBy($key, $value);
        }
        return $answer;
    }

    /**
     * @param $key
     * @param $hashKey
     * @param int $value
     * @return int
     */
    public function hIncr($key, $hashKey, int $value = 1)
    {
        $answer = -1;
        if ($this->redis !== null && $key) {
            $key = $this->parse($key);
            $answer = $this->redis->hIncrBy($key, $hashKey, $value);
        }
        return $answer;
    }

}