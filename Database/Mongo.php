<?php

namespace Yonna\Database;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Exception\BulkWriteException;
use Yonna\Exception\Exception;
use Yonna\Mapping\DBType;
use MongoDB;

class Mongo extends AbstractDB
{

    protected $db_type = DBType::MONGO;

    /**
     * @var MongoDB\Driver\Manager | null
     *
     */
    private $mongoManager = null;

    /**
     * 架构函数 取得模板对象实例
     * @access public
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        parent::__construct($setting);
        if ($this->mongoManager == null) {
            if (class_exists('\\MongoDB\Driver\Manager')) {
                try {
                    $this->mongoManager = new MongoDB\Driver\Manager($this->dsn());
                } catch (\Exception $e) {
                    $this->mongoManager = null;
                    Exception::throw('MongoDB遇到问题或未安装，请暂时停用MongoDB以减少阻塞卡顿');
                }
            }
        }
        return $this;
    }

    public function __destruct()
    {
        parent::__destruct();
    }


    public function insert(){
        $bulk = new BulkWrite();
        $bulk->insert(array(
            'product_id'        => 123,
            'product_name'      => 'zyzyzy',
            'product_price'     => 2139.00,
        ));
        try {
            $result = $this->mongoManager->executeBulkWrite('ppm.test', $bulk);
            var_dump($result->getInsertedCount());
        } catch (BulkWriteException $e) {
        }
    }

}