<?php
/**
 * Bootstrap Init Creator
 */

namespace PhpureCore\Bootstrap;

class Creator
{

    private $root = '';
    private $debug = false;
    private $env = true;
    private $timezone = 'PRC';
    private $minimum_php_version = '7.2';

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @param string $root
     */
    public function setRoot(string $root): void
    {
        $this->root = $root;
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
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @return bool
     */
    public function isEnv(): bool
    {
        return $this->env;
    }

    /**
     * @param bool $env
     */
    public function setEnv(bool $env): void
    {
        $this->env = $env;
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
     */
    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
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
     */
    public function setMinimumPhpVersion(string $minimum_php_version): void
    {
        $this->minimum_php_version = $minimum_php_version;
    }

}