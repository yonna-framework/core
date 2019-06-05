<?php

namespace PhpureCore\Console;

use Exception;

/**
 * 命令台
 * Class Console
 * @package PhpureCore\Console
 */
class Console
{

    /**
     * 获取console里的opt
     * @param $fields
     * @return array
     */
    private function getOpts(array $fields)
    {
        $optStr = implode(':', $fields) . ':';
        return getopt($optStr);
    }

    /**
     * @param $field
     * @return array|bool
     * @throws Exception
     */
    protected function getParams($field)
    {
        if (empty($field)) {
            return [];
        }
        if (!is_array($field)) {
            $field = [$field];
        }
        $opts = $this->getOpts($field);
        $err = null;
        foreach ($field as $f) {
            if (!isset($opts[$f])) {
                $err = "Params: {$f} is not Exist";
                break;
            }
        }
        if ($err !== null) {
            throw new Exception($err);
        }
        return $opts;
    }

}

