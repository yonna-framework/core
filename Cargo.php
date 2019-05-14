<?php
/**
 * Bootstrap Cargo!
 */

namespace PhpureCore;

class Cargo
{

    private $root = null;
    private $debug = null;
    private $env = null;
    private $timezone = null;
    private $minimum_php_version = null;
    private $is_window = null;
    private $memory_limit_on = null;
    private $url_separator = null;
    private $app_name = null;
    private $boot_type = null;

    /**
     * @return null
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param null $root
     */
    public function setRoot($root): void
    {
        $this->root = $root;
    }

    /**
     * @return null
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @param null $debug
     */
    public function setDebug($debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @return null
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param null $env
     */
    public function setEnv($env): void
    {
        $this->env = $env;
    }

    /**
     * @return null
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param null $timezone
     */
    public function setTimezone($timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return null
     */
    public function getMinimumPhpVersion()
    {
        return $this->minimum_php_version;
    }

    /**
     * @param null $minimum_php_version
     */
    public function setMinimumPhpVersion($minimum_php_version): void
    {
        $this->minimum_php_version = $minimum_php_version;
    }

    /**
     * @return null
     */
    public function getIsWindow()
    {
        return $this->is_window;
    }

    /**
     * @param null $is_window
     */
    public function setIsWindow($is_window): void
    {
        $this->is_window = $is_window;
    }

    /**
     * @return null
     */
    public function getMemoryLimitOn()
    {
        return $this->memory_limit_on;
    }

    /**
     * @param null $memory_limit_on
     */
    public function setMemoryLimitOn($memory_limit_on): void
    {
        $this->memory_limit_on = $memory_limit_on;
    }

    /**
     * @return null
     */
    public function getUrlSeparator()
    {
        return $this->url_separator;
    }

    /**
     * @param null $url_separator
     */
    public function setUrlSeparator($url_separator): void
    {
        $this->url_separator = $url_separator;
    }

    /**
     * @return null
     */
    public function getAppName()
    {
        return $this->app_name;
    }

    /**
     * @param null $app_name
     */
    public function setAppName($app_name): void
    {
        $this->app_name = $app_name;
    }

    /**
     * @return null
     */
    public function getBootType()
    {
        return $this->boot_type;
    }

    /**
     * @param null $boot_type
     */
    public function setBootType($boot_type): void
    {
        $this->boot_type = $boot_type;
    }



}