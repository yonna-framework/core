<?php

namespace PhpureCore\Database;

use PhpureCore\Exception\Exception;
use PhpureCore\Mapping\DBType;

class RedisCo extends Redis
{

    /**
     * @var \Swoole\Coroutine\Redis | null
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
        parent::__construct($setting);
        if ($this->redis == null) {
            if (class_exists('\Swoole\Coroutine\Redis')) {
                try {
                    $this->redis = new \Swoole\Coroutine\Redis();
                    $this->redis->connect(
                        $this->host,
                        $this->port
                    );
                    if ($this->password) {
                        $this->redis->auth($this->password);
                    }
                } catch (\Exception $e) {
                    $this->redis = null;
                    Exception::throw('Redis遇到问题或未安装，请停用Redis以减少阻塞卡顿');
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