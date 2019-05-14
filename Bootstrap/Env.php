<?php
/**
 * Bootstrap ENV Checker
 */

namespace PhpureCore\Bootstrap;

use Dotenv\Dotenv;
use Exception;
use Whoops\Handler\PrettyPageHandler as WhoopsHandlerPrettyPageHandler;
use Whoops\Run as WhoopsRun;

class Env extends AbstractClass
{

    private $creator = null;
    private $fail = '';

    protected function fail(string $msg)
    {
        $this->fail = $msg;
        return false;
    }

    protected function getFail(): string
    {
        return $this->fail;
    }

    public function __construct(Creator $creator)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * 环境初始化
     */
    private function init()
    {
        define('IS_WINDOW', strstr(PHP_OS, 'WIN') && PHP_OS !== 'CYGWIN' ? true : false);
        define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
        define("DEFAULT_TIMEZONE", $this->creator->getTimezone() ?? 'PRC');
        define('URL_SEPARATOR', '/');
        define('____', 'PureStream');
        define('_____', null);
        define('______', null);
        define('_______', null);
        // whoops
        if($this->creator->isDebug()){
            $whoops = new WhoopsRun;
            $handle = (new WhoopsHandlerPrettyPageHandler());
            $handle->setPageTitle('PHPure#Core');
            $whoops->pushHandler($handle);
            $whoops->register();
        }
        // whoops
        if($this->creator->isEnv()){
            $Dotenv = Dotenv::create(__DIR__);
            $Dotenv->load();
        }
    }

    /**
     *  检测PHP版本
     * @param string $version
     * @return bool
     */
    private function checkPHPVersion($version = '7.2')
    {
        if (version_compare(PHP_VERSION, $version, '<')) {
            return $this->fail("Need PHP >= {$version}");
        }
        return true;
    }

    /**
     *  检测ENV文件
     * @param string $version
     * @return bool
     */
    private function checkEnvFile($version = '7.2')
    {
        if (version_compare(PHP_VERSION, $version, '<')) {
            return $this->fail("Need file .env");
        }
        return true;
    }

    /**
     * 综合检查
     * @throws Exception
     */
    public function check()
    {
        if (!$this->checkPHPVersion()) throw new Exception($this->getFail());
        if (!$this->checkEnvFile()) throw new Exception($this->getFail());

        $this->init();
    }


}