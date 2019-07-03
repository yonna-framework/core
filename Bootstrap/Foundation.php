<?php

namespace Yonna\Bootstrap;

use Exception;

class Foundation
{

    private static function requireDir($dir, $qty = 0)
    {
        if (!is_dir($dir)) return 0;
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

    /**
     * @param Cargo $Cargo
     * @return Cargo
     * @throws Exception
     */
    public static function install(Cargo $Cargo): Cargo
    {
        $path = realpath($Cargo->getYonnaCorePath() . DIRECTORY_SEPARATOR . '../foundation');
        if (!$path) {
            throw new Exception('Foundation Module Not Found');
        }
        $qty = self::requireDir($path);
        $Cargo->setFoundationQty($qty);
        return $Cargo;
    }

}