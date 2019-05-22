<?php

namespace PhpureCore\Bootstrap;

use PhpureCore\Glue\Handle;

class Config
{

    /**
     * @param $path
     * @return bool|string
     */
    private static function checkPath($path)
    {
        $realpath = realpath($path);
        if (!$realpath) Handle::exception("Config Error: $path not found");
        if (is_file($realpath)) require($realpath);
        return $realpath;
    }

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

    /**
     * @param Cargo $Cargo
     * @return Cargo
     */
    public static function install(Cargo $Cargo)
    {
        $config_root = self::checkPath($Cargo->getRoot() . '/app/config');
        self::requireDir($config_root);
        $Cargo->setConfig(\PhpureCore\Config\Arrow::fetch());
        return $Cargo;
    }
}