<?php

namespace PhpureCore\Bootstrap;

use PhpureCore\Handle;

class Foundation
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
        $path = realpath($Cargo->getPureCorePath() . DIRECTORY_SEPARATOR . 'Foundation');
        if (!$path) Handle::exception('Foundation Error: root path');
        $qty = self::requireDir($path);
        $Cargo->setFoundationQty($qty);
        // diy
        $path = realpath($Cargo->getRoot() . DIRECTORY_SEPARATOR . 'foundation');
        if ($path) {
            $qty = self::requireDir($path);
            $Cargo->setFoundationDiyQty($qty);
        }
        return $Cargo;
    }

}