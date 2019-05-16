<?php
/**
 * Request
 */

namespace PhpureCore\IO;

use PhpureCore\Bootstrap\Type;
use PhpureCore\Cargo;

/**
 * Class Request
 * @package PhpureCore\IO
 */
class Request
{

    private $cargo = null;
    private $server = null;
    private $method = null;

    public function __construct(Cargo $cargo)
    {
        $this->cargo = $cargo;
        return $this;
    }

    /**
     * build Request by BootType
     */
    public function build()
    {
        switch ($this->cargo->getBootType()) {
            case Type::AJAX_HTTP:
                $this->server = $_SERVER;
                $this->method = $this->server['REQUEST_METHOD'];
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