<?php
/**
 * Bootstrap ENV Checker
 */

namespace Yonna\Bootstrap;

use Dotenv\Dotenv;
use Exception;
use Whoops\Handler\PrettyPageHandler as WhoopsHandlerPrettyPageHandler;
use Whoops\Run as WhoopsRun;

class Env
{
    const MINIMUM_PHP_VERSION = '7.3';

    /**
     * @param Cargo $Cargo
     * @return Cargo
     * @throws Exception
     */
    public static function install(Cargo $Cargo): Cargo
    {
        // dotenv
        if ($Cargo->getEnvName()) {
            if (!is_file($Cargo->getRoot() . DIRECTORY_SEPARATOR . '.env.' . $Cargo->getEnvName())) {
                exit('Need file .env.' . $Cargo->getEnvName());
            }
            $Dotenv = Dotenv::create($Cargo->getRoot(), '.env.' . $Cargo->getEnvName());
            $Dotenv->load();
        }
        // check php version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            echo 'Need PHP >= ' . self::MINIMUM_PHP_VERSION;
            exit;
        }
        // timezone
        if (!defined('TIMEZONE')) {
            define("TIMEZONE", getenv('TIMEZONE') ?? 'PRC');
        }
        date_default_timezone_set(TIMEZONE);
        // debug
        if (getenv('IS_DEBUG') === 'true') {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
            $Cargo->setDebug(true);
            //
            $whoops = new WhoopsRun;
            $handle = (new WhoopsHandlerPrettyPageHandler());
            $handle->setPageTitle('Yonna#Whoops');
            $whoops->pushHandler($handle);
            $whoops->register();
        } else {
            error_reporting(E_ERROR & E_WARNING & E_NOTICE);
            ini_set('display_errors', 'Off');
            $Cargo->setDebug(false);
        }
        // define
        if (!defined('____')) {
            define('____', '\Yonna\Foundation\YonnaStream');
        }
        if (!defined('_____')) {
            define('_____', 'yonna');
        }
        if (!defined('______')) {
            define('______', null);
        }
        if (!defined('_______')) {
            define('_______', null);
        }
        // system
        $isWindows = strstr(PHP_OS, 'WIN') && PHP_OS !== 'CYGWIN' ? true : false;
        // cargo
        $Cargo->setCurrentPhpVersion(PHP_VERSION);
        $Cargo->setEnv($_ENV);
        $Cargo->setMinimumPhpVersion(self::MINIMUM_PHP_VERSION);
        $Cargo->setWindows($isWindows);
        $Cargo->setLinux(!$isWindows);
        $Cargo->setMemoryLimitOn(function_exists('memory_get_usage'));
        $Cargo->setProjectName(getenv('PROJECT_NAME') ?? 'Yonna');
        $Cargo->setTimezone(TIMEZONE);
        return $Cargo;
    }

}