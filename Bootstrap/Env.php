<?php
/**
 * Bootstrap ENV Checker
 */

namespace PhpureCore\Bootstrap;

use Dotenv\Dotenv;
use Exception;
use Whoops\Handler\PrettyPageHandler as WhoopsHandlerPrettyPageHandler;
use Whoops\Run as WhoopsRun;

class Env
{

    private $creator = null;

    public function __construct(Creator $creator)
    {
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
        var_dump($this->creator->getRoot());
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
        $this->checkEnvFile();
        // env
        if ($this->creator->isEnv()) {
            $Dotenv = Dotenv::create($this->creator->getRoot());
            $Dotenv->load();
        }
        dump($_ENV);
        $this->checkPHPVersion($_ENV['MINIMUM_PHP_VERSION'] ?? $this->creator->getMinimumPhpVersion());

        define('IS_WINDOW', strstr(PHP_OS, 'WIN') && PHP_OS !== 'CYGWIN' ? true : false);
        define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
        define('URL_SEPARATOR', $_ENV['URL_SEPARATOR'] ?? '/');
        define('____', 'PureStream');
        define('_____', null);
        define('______', null);
        define('_______', null);
        define("APP_NAME", $_ENV['APP_NAME'] ?? 'PHPure-Project');
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
        // 加载静态库
        $this->requireDir(PATH_STATIC);
    }

}