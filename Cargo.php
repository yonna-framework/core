<?php
/**
 * Bootstrap Cargo!
 */

namespace PhpureCore;

use PhpureCore\Config\Broadcast;

class Cargo
{

    private $root = '';
    private $timezone = '';
    private $current_php_version = '';
    private $minimum_php_version = '';
    private $url_separator = '';
    private $app_name = '';
    private $boot_type = '';

    private $foundation_qty = 0;

    private $windows = false;
    private $linux = false;
    private $debug = false;
    private $load_env = false;
    private $memory_limit_on = false;

    private $env = array();
    private $config = array();

    /**
     * @param string $key
     * @param array $config
     * @return Cargo
     */
    public function setConfig(string $key, array $config): Cargo
    {
        $this->config[$key] = $config;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @param string $root
     * @return Cargo
     */
    public function setRoot(string $root): Cargo
    {
        $this->root = $root;
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
     * @return string
     */
    public function getBootType(): string
    {
        return $this->boot_type;
    }

    /**
     * @param string $boot_type
     * @return Cargo
     */
    public function setBootType(string $boot_type): Cargo
    {
        $this->boot_type = $boot_type;
        return $this;
    }

    /**
     * @return int
     */
    public function getFoundationQty(): int
    {
        return $this->foundation_qty;
    }

    /**
     * @param int $foundation_qty
     * @return Cargo
     */
    public function setFoundationQty(int $foundation_qty): Cargo
    {
        $this->foundation_qty = $foundation_qty;
        return $this;
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
     * @return bool
     */
    public function isLoadEnv(): bool
    {
        return $this->load_env;
    }

    /**
     * @param bool $load_env
     */
    public function setLoadEnv(bool $load_env): void
    {
        $this->load_env = $load_env;
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