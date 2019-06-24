<?php

namespace PhpureCore\Database;

use Exception;
use PhpureCore\Mapping\DBType;
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
                } catch (Exception $e) {
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

}