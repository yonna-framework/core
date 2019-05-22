<?php
/**
 * Bootstrap Cargo!
 */

namespace PhpureCore\Bootstrap;

class Cargo
{

    public $root = '';
    public $app_root = '';
    public $pure_core_path = '';
    public $timezone = '';
    public $current_php_version = '';
    public $minimum_php_version = '';
    public $url_separator = '';
    public $app_name = '';
    public $boot_type = '';
    public $env_name = '';

    public $function_qty = 0;
    public $function_diy_qty = 0;

    public $windows = false;
    public $linux = false;
    public $debug = false;
    public $memory_limit_on = false;

    public $env = array();
    public $config = array();

    /**
     * Cargo constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        foreach ($params as $k => $v) {
            $this->$k = $v;
        }
        $this->root = realpath($this->root);
        $this->app_root = realpath($this->root . '/app') ?? '';
        $this->pure_core_path = realpath(__DIR__ . '/..');
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getAppRoot(): string
    {
        return $this->app_root;
    }

    /**
     * @return string
     */
    public function getBootType(): string
    {
        return $this->boot_type;
    }

    /**
     * @return string
     */
    public function getEnvName(): string
    {
        return $this->env_name;
    }

    /**
     * @return string
     */
    public function getPureCorePath(): string
    {
        return $this->pure_core_path;
    }


    // -------------------------------------------------------


    /**
     * @param array $config
     * @return Cargo
     */
    public function setConfig(array $config): Cargo
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     * @return Cargo
     */
    public function setTimezone(string $timezone): Cargo
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentPhpVersion(): string
    {
        return $this->current_php_version;
    }

    /**
     * @param string $current_php_version
     * @return Cargo
     */
    public function setCurrentPhpVersion(string $current_php_version): Cargo
    {
        $this->current_php_version = $current_php_version;
        return $this;
    }

    /**
     * @return string
     */
    public function getMinimumPhpVersion(): string
    {
        return $this->minimum_php_version;
    }

    /**
     * @param string $minimum_php_version
     * @return Cargo
     */
    public function setMinimumPhpVersion(string $minimum_php_version): Cargo
    {
        $this->minimum_php_version = $minimum_php_version;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrlSeparator(): string
    {
        return $this->url_separator;
    }

    /**
     * @param string $url_separator
     * @return Cargo
     */
    public function setUrlSeparator(string $url_separator): Cargo
    {
        $this->url_separator = $url_separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppName(): string
    {
        return $this->app_name;
    }

    /**
     * @param string $app_name
     * @return Cargo
     */
    public function setAppName(string $app_name): Cargo
    {
        $this->app_name = $app_name;
        return $this;
    }

    /**
     * @return int
     */
    public function getFunctionQty(): int
    {
        return $this->function_qty;
    }

    /**
     * @param int $function_qty
     */
    public function setFunctionQty(int $function_qty): void
    {
        $this->function_qty = $function_qty;
    }

    /**
     * @return int
     */
    public function getFunctionDiyQty(): int
    {
        return $this->function_diy_qty;
    }

    /**
     * @param int $function_diy_qty
     */
    public function setFunctionDiyQty(int $function_diy_qty): void
    {
        $this->function_diy_qty = $function_diy_qty;
    }

    /**
     * @return bool
     */
    public function isWindows(): bool
    {
        return $this->windows;
    }

    /**
     * @param bool $windows
     * @return Cargo
     */
    public function setWindows(bool $windows): Cargo
    {
        $this->windows = $windows;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLinux(): bool
    {
        return $this->linux;
    }

    /**
     * @param bool $linux
     * @return Cargo
     */
    public function setLinux(bool $linux): Cargo
    {
        $this->linux = $linux;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     * @return Cargo
     */
    public function setDebug(bool $debug): Cargo
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnv(): array
    {
        return $this->env;
    }

    /**
     * @param array $env
     */
    public function setEnv(array $env): void
    {
        $this->env = $env;
    }

    /**
     * @return bool
     */
    public function isMemoryLimitOn(): bool
    {
        return $this->memory_limit_on;
    }

    /**
     * @param bool $memory_limit_on
     * @return Cargo
     */
    public function setMemoryLimitOn(bool $memory_limit_on): Cargo
    {
        $this->memory_limit_on = $memory_limit_on;
        return $this;
    }

}