<?php
/**
 * Bootstrap Init Creator
 */

namespace PhpureCore\Bootstrap;

class Creator extends AbstractClass
{

    private $root = '';
    private $debug = false;
    private $env = true;
    private $timezone = 'PRC';

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


}