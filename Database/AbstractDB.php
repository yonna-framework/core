<?php
namespace PhpureCore\Database;

use Exception;

abstract class AbstractDB
{

    /**
     * redis
     *
     * @var \Redis
     */
    private $redis = null;
    private $redisType = 'forever';

    /**
     * 加密参数
     * @var Crypto
     */
    private $crypto = null;
    private $isCrypto = false;

    /**
     * 清除所有数据
     */
    protected function resetAll()
    {
        $this->isCrypto = false;
    }

    /**
     * 获取 redis
     * @return Redis
     * @throws Exception
     */
    public function redis()
    {
        if (!$this->redis) {
            $this->redis = (new Redis());
        }
        return $this->redis;
    }

    /**
     * 获取 redis
     * @param $table
     */
    public function redisClear($table)
    {
        if (!$table) return;
        try{
            $this->redis()->delete($table);
        }catch (\Exception $e){
            // nothing
        }
    }

    /**
     * @return string
     */
    public function getRedisType(): string
    {
        return $this->redisType;
    }

    /**
     * @param string|number $redisType
     * @return Mysql|Pgsql|Mssql|Sqlite
     */
    public function setRedisType($redisType)
    {
        if(in_array($redisType, ['forever','disabled']) || is_numeric($redisType)){
            $this->redisType = $redisType;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isCrypto(): bool
    {
        return $this->isCrypto;
    }

    /**
     * @tips 一旦设为加密则只能全字而无法模糊匹配
     * @param bool $isCrypto
     * @return Mysql|Pgsql|Mssql|Sqlite
     */
    public function setIsCrypto(bool $isCrypto)
    {
        $this->isCrypto = $isCrypto;
        return $this;
    }

    /**
     * @return Crypto
     */
    private function getCrypto(){
        if(!$this->crypto){
            $config = CONFIG['crypto'];
            if(!$config){
                $config = array(
                    'type' => 'des-cbc',
                    'secret' => 'hunzsig#',
                    'iv' => 'hun0Zsig',
                );
            } else {
                $config = array(
                    'type' => CONFIG['crypto']['type'],
                    'secret' => strrev(CONFIG['crypto']['secret']),
                    'iv' => strrev(CONFIG['crypto']['iv']),
                );
            }
            $this->crypto = new Crypto($config);
        }
        return $this->crypto;
    }

}
