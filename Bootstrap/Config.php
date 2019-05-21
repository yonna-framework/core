<?php

namespace PhpureCore\Bootstrap;

use PhpureCore\Config\{Arrow};
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
     * @param Cargo $Cargo
     * @return Cargo
     */
    public static function install(Cargo $Cargo)
    {
        $config_root = self::checkPath($Cargo->getRoot() . '/app/config');
        self::checkPath($config_root . DIRECTORY_SEPARATOR . 'broadcast.php');
        self::checkPath($config_root . DIRECTORY_SEPARATOR . 'crontab.php');
        self::checkPath($config_root . DIRECTORY_SEPARATOR . 'crypto.php');
        self::checkPath($config_root . DIRECTORY_SEPARATOR . 'database.php');
        self::checkPath($config_root . DIRECTORY_SEPARATOR . 'event.php');
        self::checkPath($config_root . DIRECTORY_SEPARATOR . 'log.php');
        self::checkPath($config_root . DIRECTORY_SEPARATOR . 'middleware.php');
        self::checkPath($config_root . DIRECTORY_SEPARATOR . 'scope.php');
        $Cargo->setConfig(Arrow::fetch());
        return $Cargo;
    }
}