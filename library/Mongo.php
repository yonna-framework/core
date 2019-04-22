<?php
/**
 * 数据库连接类，依赖 MONGODB 扩展
 */

namespace library;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Exception\InvalidArgumentException;
use MongoDB\Driver\Exception\RuntimeException;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;

class Mongo
{

    /**
     * mongo驱动管理
     *
     * @var Manager
     */
    private $manager;

    /**
     * dsn
     *
     * @var string
     */
    private $dsn;

    /**
     * 数据库地址名称密码等配置
     *
     * @var array
     */
    private $settings = array();

    /**
     * sql 的参数
     *
     * @var array
     */
    private $parameters = array();

    /**
     * 最后一条执行的 js
     *
     * @var string
     */
    private $lastJs = '';

    /**
     * 参数
     *
     * @var array
     */
    private $_options = array();

    /**
     * 错误信息
     *
     * @var
     */
    private $error;

    /**
     * 多重嵌套事务处理堆栈
     */
    private $_transTrace = 0;

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

    /**
     * 条件对象，实现无敌闭包
     *
     * @var array
     */
    private $_where = array();
    private $_where_table = '';

    /**
     * 排序类型设置
     */
    const DESC = 'desc';
    const ASC = 'asc';

    /**
     * 临时字段寄存
     */
    private $_currentFieldType = array();
    private $_tempFieldType = array();

    /**
     * 清除所有数据
     */
    protected function resetAll()
    {
        $this->_options = array();
        $this->_where = array();
        $this->_where_table = '';
        $this->_currentFieldType = array();
        $this->_tempFieldType = array();
        $this->parameters = array();
        $this->lastJs = '';
        $this->error = '';
        parent::resetAll();
    }

    /**
     * 构造方法
     *
     * @param $host
     * @param $port
     * @param $user
     * @param $password
     * @param string $db_name
     * @param string $charset
     */
    public function __construct($host, $port, $user, $password, $db_name, $charset)
    {
        $this->settings = array(
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'password' => $password,
            'dbname' => $db_name,
            'charset' => $charset ?: 'utf8',
        );
        $this->dsn();
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        $this->managerFree();
    }

    /**
     * 获取 DSN
     */
    private function dsn()
    {
        if (!$this->dsn) {
            if (empty($this->settings['user']) || empty($this->settings['password'])) {
                $this->dsn = "mongodb://{$this->settings['host']}:{$this->settings['path']}";
            } else {
                $this->dsn = "mongodb://{$this->settings['user']}:{$this->settings['password']}@{$this->settings['host']}:{$this->settings['path']}";
            }
        }
        return $this->dsn;
    }

    /**
     * 获取 Manager
     */
    private function manager()
    {
        if (!$this->manager) {
            try {
                $this->manager = new Manager($this->dsn());
            } catch (InvalidArgumentException $e) {
                exit($e->getMessage());
            } catch (RuntimeException $e) {
                exit($e->getMessage());
            }
        }
        return $this->manager;
    }

    /**
     * 释放 Manager
     */
    private function managerFree()
    {
        if (!empty($this->manager)) {
            $this->manager = null;
        }
    }

    /**
     * 数据库错误信息
     * @param $err
     * @return bool
     */
    private function error($err)
    {
        $this->error = $err;
        return false;
    }

    /**
     * 获取数据库错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 返回 lastInsertId
     *
     * @return string
     */
    public function lastInsertId()
    {
        return $this->manager()->lastInsertId();
    }


}

try {

    /*
    $m1 = new MongoClient('mongodb://localhost:27017');
    $m1db = $m1->selectDB('test');
    try{
        $m1col = $m1db->selectCollection('test1');
        $m1col->insert(array('name' => 'mzy', 'timestamp' => time()));
        print_r($m1);
        print_r($m1db);
        print_r($m1col);
        $cursor = $m1col
            ->find(array('timestamp' => 1555320708))
            ->sort(array('timestamp' => -1));
    }catch (Exception $e){
        exit($e->getMessage());
    }
    $result = array();
    foreach ($cursor as $document) {
        // print_r($document['_id']->id);
        $result[] = $document;
    }
    print_r($result);
    */

    $filter = [];// ['a' => ['$eq' => 1]];
    $options = [
        'sort' => ['x' => -1],
    ];
    $m2 = new Manager('mongodb://root:123456@localhost:27017');

    $bulk = new BulkWrite;
    // $bulk->insert(['name' => '[MY_TIME=]' . time(), 'timestamp' => time()]);
    $bulk->insert(['a' => 'veryfun', 'b' => 1]);
    $bulk->update(
        ['a' => 1],
        ['$set' => ['b' => 'b+1']]
    );
    $writeResult = $m2->executeBulkWrite('test.test1', $bulk);
    print_r($writeResult);
    var_dump(count($bulk));

    $query = new Query($filter, $options);
    $cursor = $m2->executeQuery('test.test1', $query);
    $result = array();
    foreach ($cursor as $document) {
        $temp = (array)$document;
        if (!empty($temp['_id'])) {
            $temp['_id'] = (array)($temp['_id']);
            $temp['_id'] = $temp['_id']['oid'];
        }
        $result[] = $temp;
    }
    print_r($result);

    var_dump(count($bulk));

} catch (\MongoDB\Driver\Exception\Exception $e) {
    echo($e->getMessage());
}


