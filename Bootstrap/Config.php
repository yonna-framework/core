<?php

namespace PhpureCore\Bootstrap;

use Exception;
use PhpureCore\Cargo;
use PhpureCore\Config\{Arrow, Broadcast, Crontab, Crypto, Database, Event, Log, Middleware, Scope};

class Config
{

    private $config_root = null;
    private $cargo = null;

    public function __construct(Cargo $cargo)
    {
        $this->cargo = $cargo;
        return $this;
    }

    /**
     * @param $path
     * @throws Exception
     */
    private function checkPath($path)
    {
        $realpath = realpath($path);
        if (!$realpath) throw new Exception("Config Error: $path not found");
        if (is_file($realpath)) require($realpath);
        return $realpath;
    }

    /**
     * 配置初始化
     * @throws Exception
     */
    public function init()
    {
        $this->config_root = $this->checkPath($this->cargo->getRoot() . DIRECTORY_SEPARATOR . 'config');
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'broadcast.php');
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'crontab.php');
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'crypto.php');
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'database.php');
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'event.php');
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'log.php');
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'middleware.php');
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'scope.php');
        $this->cargo->setConfig(Arrow::fetch());
        return $this->cargo;
    }
}