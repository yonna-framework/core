<?php
/**
 * Bootstrap Init Creator
 */

namespace PhpureCore\Bootstrap;

class Creator
{

    private $root = '';
    private $env = 'example';
    private $boot_type = BootType::AJAX_HTTP;

    public function __construct($root, $env = null, $boot_type = null)
    {
        $this->root = $root;
        $env && $this->env = $env;
        $boot_type && $this->boot_type = $boot_type;
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
    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * @return string
     */
    public function getBootType(): string
    {
        return $this->boot_type;
    }

}