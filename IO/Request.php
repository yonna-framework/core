<?php
/**
 * Request
 */

namespace PhpureCore\IO;

use PhpureCore\Bootstrap\Type;
use PhpureCore\Cargo;
use PhpureCore\Handle;

/**
 * Class Request
 * @package PhpureCore\IO
 */
class Request
{

    public $crypto = false;
    public $cargo = null;
    public $header = null;
    public $method = null;
    public $body = null;
    public $input = array();
    public $file = null;

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
                $header = array();
                foreach ($_SERVER as $k => $v) {
                    if (strpos($k, 'HTTP_') === 0) {
                        $header[strtolower(str_replace('HTTP_', '', $k))] = $v;
                    }
                }
                $this->header = $header;
                $this->method = $_SERVER['REQUEST_METHOD'];
                $this->body = $_POST['body'] ?? file_get_contents('php://input');
                $this->file = parseFileData($_FILES);
                break;
            case Type::SWOOLE_HTTP:
                break;
            case Type::SWOOLE_WEB_SOCKET:
                break;
            case Type::SWOOLE_TCP:
                break;
            default:
                Handle::exception('Request invalid boot type');
                break;
        }
        // 解密协议
        // Crypto::cipherMethods();
        $this->crypto = Crypto::isCrypto($this);
        if ($this->crypto === true) {
            $this->input = Crypto::input($this);
            $this->input = json_decode($this->input, true) ?? array();
        } else {
            $this->input = json_decode($this->body, true) ?? array();
        }
        dd($this);
        return $this;
    }

}