<?php
/**
 * Bootstrap ENV Checker
 */

namespace PhpureCore\Bootstrap;

use Dotenv\Dotenv;
use Whoops\Handler\PrettyPageHandler as WhoopsHandlerPrettyPageHandler;
use Whoops\Run as WhoopsRun;
use PhpureCore\Glue\Handle;

class Env
{

    const MINIMUM_PHP_VERSION = '7.2';

    public static function install(Cargo $Cargo)
    {
        // 检测ENV文件
        if ($Cargo->getEnvName()) {
            if (!is_file($Cargo->getRoot() . DIRECTORY_SEPARATOR . '.env.' . $Cargo->getEnvName())) {
                Handle::exception('Need file .env.' . $Cargo->getEnvName());
            }
            $Dotenv = Dotenv::create($Cargo->getRoot(), '.env.' . $Cargo->getEnvName());
            $Dotenv->load();
        }
        // 检测PHP版本
        $minimum_php_version = getenv('MINIMUM_PHP_VERSION') ?? self::MINIMUM_PHP_VERSION;
        if (version_compare(PHP_VERSION, $minimum_php_version, '<')) {
            Handle::exception("Need PHP >= {$minimum_php_version}");
        }
        $Cargo->setCurrentPhpVersion(PHP_VERSION);
        define('____', 'PureStream');
        define('_____', null);
        define('______', null);
        define('_______', null);
        define("TIMEZONE", getenv('TIMEZONE') ?? 'PRC');
        // whoops
        if ((getenv('IS_DEBUG') && getenv('IS_DEBUG') === 'true')) {
            $whoops = new WhoopsRun;
            $handle = (new WhoopsHandlerPrettyPageHandler());
            $handle->setPageTitle('PHPure#Core');
            $whoops->pushHandler($handle);
            $whoops->register();
        }
        // system
        $isWindows = strstr(PHP_OS, 'WIN') && PHP_OS !== 'CYGWIN' ? true : false;
        // timezone
        date_default_timezone_set(TIMEZONE);
        // cargo
        $Cargo->setDebug(getenv('IS_DEBUG') === 'true' || $Cargo->isDebug());
        $Cargo->setEnv($_ENV);
        $Cargo->setMinimumPhpVersion($minimum_php_version);
        $Cargo->setWindows($isWindows);
        $Cargo->setLinux(!$isWindows);
        $Cargo->setMemoryLimitOn(function_exists('memory_get_usage'));
        $Cargo->setUrlSeparator(getenv('URL_SEPARATOR') ?? '/');
        $Cargo->setAppName(getenv('APP_NAME') ?? 'PHPure-Project');
        $Cargo->setTimezone(TIMEZONE);
        return $Cargo;
    }

}