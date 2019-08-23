<?php
/**
 * Request
 */

namespace Yonna\IO;

use SimpleXMLElement;
use Yonna\Foundation\Parse;
use Yonna\Bootstrap\BootType;
use Yonna\Bootstrap\Cargo;
use Yonna\Throwable\Exception;

/**
 * Class Request
 * @package Core\Core\IO
 */
class Request
{
    /**
     * @var Cargo
     */
    private $cargo = null;

    private $local = false;
    private $header = null;
    private $cookie = null;
    private $method = null;
    private $content_type = null;
    private $user_agent = null;
    private $scope = '';

    private $client_id = null;
    private $host = null;
    private $ssl = false;
    private $ip = '127.0.0.1';
    private $port = 80;

    private $raw = '';
    private $input = '';
    private $input_type = InputType::UN_KNOW;
    private $file = null;
    private $stack = '';

    /**
     * @return Cargo
     */
    public function getCargo(): Cargo
    {
        return $this->cargo;
    }

    /**
     * @return bool
     */
    public function isLocal(): bool
    {
        return $this->local;
    }

    /**
     * @return array|null
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return mixed
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->content_type;
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     */
    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @return mixed|string|null
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @return bool
     */
    public function isSsl(): bool
    {
        return $this->ssl;
    }

    /**
     * @return mixed|string|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * @param $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * @return false|string|null
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return string
     */
    public function getInputType(): string
    {
        return $this->input_type;
    }

    /**
     * @return array|null
     */
    public function getFile(): ?array
    {
        return $this->file;
    }

    /**
     * @return mixed|string
     */
    public function getStack()
    {
        return $this->stack;
    }


    /**
     * Request constructor.
     * @param object $cargo
     */
    public function __construct(object $cargo)
    {
        $this->cargo = $cargo;
        $rawData = null;
        switch ($this->cargo->getBootType()) {
            case BootType::AJAX_HTTP:
                $server = [];
                $this->header = [];
                foreach ($_SERVER as $k => $v) {
                    $server[strtolower($k)] = $v;
                    if (strpos($k, 'HTTP_') === 0) {
                        $this->header[strtolower(str_replace('HTTP_', '', $k))] = $v;
                    }
                }
                $this->cookie = $_COOKIE;
                $this->method = strtoupper($server['request_method']);
                $this->user_agent = $this->header['user_agent'];
                $this->content_type = !empty($server['content_type']) ? strtolower(explode(';', $server['content_type'])[0]) : null;
                $this->file = Parse::fileData($_FILES);
                $rawData = file_get_contents('php://input');
                break;
            case BootType::SWOOLE_HTTP:
                $extend = $this->cargo->getExtend();
                $server = $extend['request']['server'];
                $this->header = [];
                foreach ($extend['request']['header'] as $hk => $hv) {
                    $this->header[str_replace('-', '_', $hk)] = $hv;
                }
                $this->cookie = $extend['request']['cookie'];
                $this->method = strtoupper($server['request_method']);
                $this->user_agent = $this->header['user_agent'];
                $this->content_type = !empty($this->header['content_type']) ? strtolower(explode(';', $this->header['content_type'])[0]) : null;
                $this->file = Parse::fileData($extend['request']['files']);
                $rawData = $extend['request']['rawData'];
                break;
            case BootType::SWOOLE_WEB_SOCKET:
                break;
            case BootType::SWOOLE_TCP:
                break;
            default:
                Exception::throw('Request invalid boot type');
                break;
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
            Exception::throw('ip yonna');
        }
        // SSL
        if ($this->ssl === false && ($server['request_scheme'] ?? '') === 'https') {
            $this->ssl = true;
        }
        if ($this->ssl === false && strpos(($server['server_protocol'] ?? ''), 'https') !== false) {
            $this->ssl = true;
        }
        // HOST / PORT
        $this->host = $header['x_host'] ?? $header['host'] ?? $server['server_name'] ?? null;
        if ($this->host) {
            $this->port = explode(':', $this->host);
            $this->port = $this->port[1] ?? 80;
            if (strpos($this->host, ':') === false || strpos($this->host, ':') > 6) {
                $this->host = ($this->ssl ? 'http' : 'https') . '://' . $this->host;
            }
        }
        // 处理协议，将可能的数据转为json字符串记录在input
        switch ($this->method) {
            case 'GET':
                switch ($this->content_type) {
                    case null:
                        $this->raw = $_GET;
                        break;
                    case 'application/x-www-form-urlencoded':
                        parse_str($rawData, $temp);
                        $this->raw = $temp;
                        break;
                    case 'text/plain':
                    case 'application/json':
                        $this->raw = $rawData;
                        break;
                    case 'application/xml':
                    case 'text/xml':
                        $this->raw = simplexml_load_string($rawData);
                        break;
                    default:
                        Exception::throw("not support {$this->content_type} yet");
                        break;
                }
                break;
            case 'POST':
                switch ($this->content_type) {
                    case null:
                    case 'multipart/form-data':
                    case 'application/x-www-form-urlencoded':
                        $this->raw = $_POST;
                        break;
                    case 'text/plain':
                    case 'application/json':
                        $this->raw = $rawData;
                        break;
                    case 'application/xml':
                    case 'text/xml':
                        $this->raw = simplexml_load_string($rawData);
                        break;
                    default:
                        Exception::throw("not support {$this->content_type} yet");
                        break;
                }
                break;
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                switch ($this->content_type) {
                    case 'text/plain':
                    case 'application/json':
                        $this->raw = $rawData;
                        break;
                    case 'application/xml':
                    case 'text/xml':
                        $this->raw = simplexml_load_string($rawData);
                        break;
                    default:
                        Exception::throw("not support {$this->content_type} yet");
                        break;
                }
                break;
            default:
                Exception::throw("not support {$this->method} yet");
                break;
        }
        if ($this->raw instanceof SimpleXMLElement) {
            $this->input_type = InputType::XML;
            $this->raw = json_encode($this->raw);
        } else if (is_array($this->raw)) {
            $this->input_type = InputType::FORM;
            $this->raw = json_encode($this->raw);
        } else {
            $this->input_type = InputType::RAW;
        }
        return Crypto::input($this);
    }

}