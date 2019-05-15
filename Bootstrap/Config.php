<?php

namespace PhpureCore\Bootstrap;

use Exception;
use PhpureCore\Cargo;
use PhpureCore\Config\Broadcast;

class Config
{

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
    }

    private function broadcast()
    {
        $this->checkPath($this->cargo->getRoot() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'broadcast.php');
        $this->cargo->setConfig('broadcast', Broadcast::fetch());
    }

    private function crontab()
    {

    }

    private function crypto()
    {

    }

    private function db()
    {

    }

    private function event()
    {

    }

    private function log()
    {

    }

    private function middleware()
    {

    }

    private function scope()
    {

    }

    /**
     * 配置初始化
     * @throws Exception
     */
    public function init()
    {
        $this->checkPath($this->cargo->getRoot() . DIRECTORY_SEPARATOR . 'config');
        $this->broadcast();
        return $this->cargo;
    }
}