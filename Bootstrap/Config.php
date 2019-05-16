<?php

namespace PhpureCore\Bootstrap;

use Exception;
use PhpureCore\Cargo;
use PhpureCore\Config\{Broadcast};

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

    private function broadcast()
    {
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'broadcast.php');
        $this->cargo->setConfig('broadcast', Broadcast::fetch());
    }

    private function crontab()
    {
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'crontab.php');
        $this->cargo->setConfig('crontab', Crontab::fetch());
    }

    private function crypto()
    {
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'crypto.php');
        $this->cargo->setConfig('crypto', Crypto::fetch());
    }

    private function database()
    {
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'database.php');
        $this->cargo->setConfig('database', Database::fetch());
    }

    private function event()
    {
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'event.php');
        $this->cargo->setConfig('event', Event::fetch());
    }

    private function log()
    {
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'log.php');
        $this->cargo->setConfig('log', Log::fetch());
    }

    private function middleware()
    {
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'middleware.php');
        $this->cargo->setConfig('middleware', Middleware::fetch());
    }

    private function scope()
    {
        $this->checkPath($this->config_root . DIRECTORY_SEPARATOR . 'scope.php');
        $this->cargo->setConfig('scope', Scope::fetch());
    }

    /**
     * 配置初始化
     * @throws Exception
     */
    public function init()
    {
        $this->config_root = $this->checkPath($this->cargo->getRoot() . DIRECTORY_SEPARATOR . 'config');
        $this->broadcast();
        $this->crontab();
        $this->crypto();
        $this->database();
        $this->event();
        $this->log();
        $this->middleware();
        $this->scope();
        return $this->cargo;
    }
}