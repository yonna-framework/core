<?php
/**
 * Request
 */

namespace PhpureCore\IO;

use PhpureCore\Bootstrap\Type;
use PhpureCore\Cargo;

class Request
{

    private $cargo = null;
    private $server = null;
    private $method = null;

    public function __construct(Cargo $cargo)
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->cargo = $cargo;
        $this->server = $_SERVER;
        return $this;
    }

    /**
     * build Request by BootType
     */
    public function build()
    {
        switch ($this->cargo->getBootType()) {
            case Type::AJAX_HTTP:
                break;
            case Type::SWOOLE_HTTP:
                break;
            case Type::SWOOLE_WEB_SOCKET:
                break;
            case Type::SWOOLE_TCP:
                break;
            default:
                exit('Error IO');
                break;
        }
        return $this;
    }

}