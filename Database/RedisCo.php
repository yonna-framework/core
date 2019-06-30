<?php

namespace Yonna\Database;

use Yonna\Exception\Exception;

class RedisCo extends Redis
{

    /**
     * 架构函数 取得模板对象实例
     * @access public
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        $RedisDriver = null;
        if (class_exists('\Swoole\Coroutine\Redis')) {
            try {
                $RedisDriver = new \Swoole\Coroutine\Redis();
            } catch (\Exception $e) {
                Exception::throw('RedisSwoole遇到问题或未安装，请该用原生Redis拓展或停用Redis以减少阻塞卡顿');
            }
        }
        parent::__construct($setting, $RedisDriver);
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

}