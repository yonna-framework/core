<?php
/**
 * 数据库连接类，依赖 PDO_PGSQL 扩展
 * version > 9.7
 */

namespace Yonna\Database\Pgsql;

use Exception;
use Yonna\Mapping\DBType;

class Schemas
{

    private $setting = null;
    private $options = null;

    /**
     * Schemas constructor.
     * @param array $setting
     * @param array $options
     */
    public function __construct(array $setting, array $options)
    {
        $this->setting = $setting;
        $this->options = $options;
    }

    /**
     * 析构方法
     * @access public
     */
    public function __destruct()
    {
        $this->setting = null;
        $this->options = null;
    }

    /**
     * 哪个表
     *
     * @param string $table
     * @return Table
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
        return Core::get(Table::class, $this->setting, $this->options);
    }

}
