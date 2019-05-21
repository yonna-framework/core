<?php

namespace PhpureCore\Bootstrap;

use PhpureCore\Glue\Handle;

class Functions
{

    /**
     * @param $dir
     * @param int $qty
     * @return int|void
     */
    private static function requireDir($dir, $qty = 0)
    {
        if (!is_dir($dir)) return;
        $files = opendir($dir);
        while ($file = readdir($files)) {
            if ($file != '.' && $file != '..') {
                $realFile = $dir . '/' . $file;
                if (is_dir($realFile)) {
                    $qty = self::requireDir($realFile, $qty);
                } elseif (strpos($file, '.php') === false) {
                    continue;
                } else {
                    require_once($realFile);
                    $qty++;
                }
            }
        }
        closedir($files);
        return $qty;
    }

    public static function install(Cargo $Cargo)
    {
        $path = realpath($Cargo->getPureCorePath() . DIRECTORY_SEPARATOR . 'Functions');
        if (!$path) Handle::exception('Functions Error: root path');
        $qty = self::requireDir($path);
        $Cargo->setFunctionQty($qty);
        // diy
        $path = realpath($Cargo->getRoot() . DIRECTORY_SEPARATOR . 'functions');
        if ($path) {
            $qty = self::requireDir($path);
            $Cargo->setFunctionDiyQty($qty);
        }
        return $Cargo;
    }

}