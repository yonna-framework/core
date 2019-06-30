<?php

namespace Yonna\Console;

use Exception;

/**
 * 命令台
 * Class Console
 * @package Core\Core\Console
 */
class Console
{

    /**
     * @param $opts
     * @param $field
     * @return array|bool
     * @throws Exception
     */
    protected function checkParams($opts, $field)
    {
        if (empty($field)) {
            return [];
        }
        if (!is_array($field)) {
            $field = [$field];
        }
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
        return true;
    }

}

