<?php
/**
 * Request
 */

namespace PhpureCore\IO;

use PhpureCore\Mapping\BootType;
use PhpureCore\Glue\Handle;

/**
 * Class Request
 * @package PhpureCore\IO
 */
class Request
{
    /**
     * @var \PhpureCore\Bootstrap\Cargo
     */
    public $cargo = null;

    public $crypto = false;
    public $local = false;
    public $header = null;
    public $method = null;
    public $content_type = null;
    public $body = null;
    public $input = array();
    public $file = null;
    public $client_id = null;
    public $ip = '127.0.0.1';
    public $stack = null;

    /**
     * build Request by BootType
     */
    private function init()
    {
        switch ($this->cargo->getBootType()) {
            case BootType::AJAX_HTTP:
                $header = array();
                $server = array();
                foreach ($_SERVER as $k => $v) {
                    $server[strtolower($k)] = $v;
                    if (strpos($k, 'HTTP_') === 0) {
                        $header[strtolower(str_replace('HTTP_', '', $k))] = $v;
                    }
                }
                $this->header = $header;
                $this->method = strtoupper($server['request_method']);
                $this->content_type = !empty($server['content_type']) ? strtolower(explode(';', $server['content_type'])[0]) : null;
                $this->file = parse_fileData($_FILES);
                break;
            case BootType::SWOOLE_HTTP:
                break;
            case BootType::SWOOLE_WEB_SOCKET:
                break;
            case BootType::SWOOLE_TCP:
                break;
            default:
                Handle::exception('Request invalid boot type');
                break;
        }
        if (!Crypto::checkToken($this)) {
            Handle::notPermission('welcome');
        }
        //
        $this->client_id = $this->header['client_id'] ?? '';
        $this->stack = $this->header['stack'] ?? '';
        // IP
        $ip = null;
        $ip === null && $ip = $this->header['x_real_ip'] ?? null;
        $ip === null && $ip = $this->header['client_ip'] ?? null;
        $ip === null && $ip = $this->header['x_forwarded_for'] ?? null;
        $ip === null && $ip = $server['remote_addr'] ?? null;
        $ip && $this->ip = $ip;
        $this->local = ($ip === '127.0.0.1');
        if (!$this->ip) {
            Handle::notPermission('iam pure');
        }
        // 解密协议
        // Crypto::cipherMethods();
        switch ($this->method) {
            case 'GET':
                switch ($this->content_type) {
                    case 'multipart/form-data':
                        $body = $_GET['body'] ?? null;
                        $this->body = is_string($body) ? $body : json_encode($_GET);
                        break;
                    default:
                        $this->body = file_get_contents('php://input') ?? '';
                        break;
                }
                break;
            case 'POST':
                switch ($this->content_type) {
                    case 'multipart/form-data':
                        $body = $_POST['body'] ?? null;
                        $this->body = is_string($body) ? $body : json_encode($_POST);
                        break;
                    default:
                        $this->body = file_get_contents('php://input') ?? '';
                        break;
                }
                break;
            case 'DEFAULT':
                Handle::exception('method error');
                break;
        }
        $this->crypto = Crypto::isCrypto($this);
        if ($this->crypto === true) {
            $this->input = Crypto::input($this);
            $this->input = json_decode($this->input, true) ?? array();
        } else {
            $this->input = json_decode($this->body, true) ?? array();
        }
    }

    public function __construct(object $cargo)
    {
        $this->cargo = $cargo;
        $this->init();
        return $this;
    }

}