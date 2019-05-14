<?php
/**
 * Bootstrap ENV Checker
 */

namespace PhpureCore\Bootstrap;

use Dotenv\Dotenv;
use Exception;
use PhpureCore\Cargo;
use Whoops\Handler\PrettyPageHandler as WhoopsHandlerPrettyPageHandler;
use Whoops\Run as WhoopsRun;

class Env
{

    private $cargo = null;
    private $creator = null;

    public function __construct(Cargo $cargo, Creator $creator)
    {
        $this->cargo = $cargo;
        $this->creator = $creator;
        return $this;
    }

    /**
     *  检测PHP版本
     * @param string $version
     * @return bool
     * @throws Exception
     */
    private function checkPHPVersion($version = '7.2')
    {
        if (version_compare(PHP_VERSION, $version, '<')) {
            throw new Exception("Need PHP >= {$version}");
        }
        return true;
    }

    /**
     *  检测ENV文件
     * @return bool
     * @throws Exception
     */
    private function checkEnvFile()
    {
        if (!is_file($this->creator->getRoot() . '/.env')) {
            throw new Exception("Need file .env");
        }
        return true;
    }

    /**
     * 环境初始化
     * @throws Exception
     */
    public function init()
    {
        // env
        if ($this->creator->isEnv()) {
            $this->checkEnvFile();
            $Dotenv = Dotenv::create($this->creator->getRoot());
            $Dotenv->load();
        }
        $minimum_php_version = $_ENV['MINIMUM_PHP_VERSION'] ?? $this->creator->getMinimumPhpVersion();
        $this->checkPHPVersion($minimum_php_version);
        define('____', 'PureStream');
        define('_____', null);
        define('______', null);
        define('_______', null);
        define("TIMEZONE", $_ENV['TIMEZONE'] ?? $this->creator->getTimezone() ?? 'PRC');
        // whoops
        if ($_ENV['IS_DEBUG'] === 'true' || $this->creator->isDebug()) {
            $whoops = new WhoopsRun;
            $handle = (new WhoopsHandlerPrettyPageHandler());
            $handle->setPageTitle('PHPure#Core');
            $whoops->pushHandler($handle);
            $whoops->register();
        }
        // 设置时区
        date_default_timezone_set(TIMEZONE);
        // cargo
        $this->cargo->setRoot($this->creator->getRoot());
        $this->cargo->setDebug($_ENV['IS_DEBUG'] === 'true' || $this->creator->isDebug());
        $this->cargo->setEnv($this->creator->isEnv());
        $this->cargo->setMinimumPhpVersion($minimum_php_version);
        $this->cargo->setIsWindow(strstr(PHP_OS, 'WIN') && PHP_OS !== 'CYGWIN' ? true : false);
        $this->cargo->setMemoryLimitOn(function_exists('memory_get_usage'));
        $this->cargo->setUrlSeparator($_ENV['URL_SEPARATOR'] ?? '/');
        $this->cargo->setAppName($_ENV['APP_NAME'] ?? 'PHPure-Project');
        $this->cargo->setTimezone(TIMEZONE);
        return $this->cargo;
    }

}