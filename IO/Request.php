<?php
/**
 * Request
 */

namespace Yonna\IO;

use SimpleXMLElement;
use Yonna\Bootstrap\Cargo;
use Yonna\Foundation\Parse;
use Yonna\Bootstrap\BootType;
use Yonna\Throwable\Exception;

/**
 * Class Request
 * @package Core\Core\IO
 */
class Request extends RequestBuilder
{

    /**
     * 读取全局变量
     */
    private function loadGlobal()
    {
        $this->setGet($_GET ?? []);
        $this->setPost($_POST ?? []);
        $this->setRequest($_REQUEST ?? []);
        $this->setFiles($_FILES ? Parse::fileData($_FILES) : []);
        $this->setCookie($_COOKIE ?? []);
        $this->setSession($_SESSION ?? []);
        $this->setRawData(file_get_contents('php://input') ?? $GLOBALS['HTTP_RAW_POST_DATA'] ?? '');
        $this->setPhpSelf($_SERVER['PHP_SELF']);
        $this->setGatewayInterface($_SERVER['GATEWAY_INTERFACE']);
        $this->setRequestMethod($_SERVER['REQUEST_METHOD']);
        $this->setRequestTime($_SERVER['REQUEST_TIME']);
        $this->setRequestTimeFloat($_SERVER['REQUEST_TIME_FLOAT']);
        $this->setQueryString($_SERVER['QUERY_STRING']);
        $this->setDocumentRoot($_SERVER['DOCUMENT_ROOT']);
        $this->setHttpAccept($_SERVER['HTTP_ACCEPT']);
        $this->setHttpAcceptCharset($_SERVER['HTTP_ACCEPT_CHARSET']);
        $this->setHttpAcceptEncoding($_SERVER['HTTP_ACCEPT_ENCODING']);
        $this->setHttpAcceptLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $this->setHttpConnection($_SERVER['HTTP_CONNECTION']);
        $this->setHttpHost($_SERVER['HTTP_HOST']);
        $this->setHttpReferer($_SERVER['HTTP_REFERER']);
        $this->setHttpUserAgent($_SERVER['HTTP_USER_AGENT']);
        $this->setHttps($_SERVER['HTTPS']);
        $this->setRemoteAddr($_SERVER['REMOTE_ADDR']);
        $this->setRemoteHost($_SERVER['REMOTE_HOST']);
        $this->setRemotePort($_SERVER['REMOTE_PORT']);
        $this->setRemoteUser($_SERVER['REMOTE_USER']);
        $this->setRedirectRemoteUser($_SERVER['REDIRECT_REMOTE_USER']);
        $this->setScriptFilename($_SERVER['SCRIPT_FILENAME']);
        $this->setServerAddr($_SERVER['SERVER_ADDR']);
        $this->setServerName($_SERVER['SERVER_NAME']);
        $this->setServerSoftware($_SERVER['SERVER_SOFTWARE']);
        $this->setServerProtocol($_SERVER['SERVER_PROTOCOL']);
        $this->setServerAdmin($_SERVER['SERVER_ADMIN']);
        $this->setServerPort($_SERVER['SERVER_PORT']);
        $this->setServerSignature($_SERVER['SERVER_SIGNATURE']);
        $this->setPathTranslated($_SERVER['PATH_TRANSLATED']);
        $this->setScriptName($_SERVER['SCRIPT_NAME']);
        $this->setRequestUri($_SERVER['REQUEST_URI']);
        $this->setPhpAuthDigest($_SERVER['PHP_AUTH_DIGEST']);
        $this->setPhpAuthUser($_SERVER['PHP_AUTH_USER']);
        $this->setPhpAuthPw($_SERVER['PHP_AUTH_PW']);
        $this->setAuthType($_SERVER['AUTH_TYPE']);
        $this->setOrigPathInfo($_SERVER['ORIG_PATH_INFO']);
        $this->setPathInfo($_SERVER['PATH_INFO']);
    }

    /**
     * 读取拓展 RequestBuilder
     * @param RequestBuilder $requestBuilder
     */
    private function loadRequestBuilder(RequestBuilder $requestBuilder)
    {
        $this->cover($requestBuilder);
    }

    /**
     * Request constructor.
     * @param Cargo $cargo
     * @param RequestBuilder|null $requestBuilder
     * @throws Exception\ThrowException
     */
    public function __construct(Cargo $cargo, RequestBuilder $requestBuilder = null)
    {
        // load cargo
        $this->cargo = $cargo;
        // load global
        $this->loadGlobal();
        // load builder
        if ($requestBuilder != null && $requestBuilder instanceof RequestBuilder) {
            $this->loadRequestBuilder($requestBuilder);
        }
        $rawData = null;
        switch ($this->cargo->getBootType()) {
            case BootType::AJAX_HTTP:
                $server = [];
                $this->header = [];
                foreach ($_SERVER as $hk => $hv) {
                    $hk = strtolower($hk);
                    $server[$hk] = $hv;
                    if (strpos($hk, 'http_') === 0) {
                        $this->header[str_replace('http_', '', $hk)] = $hv;
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
                $extend = $this->cargo->getExtend();
                $server = $extend['request']['server'];
                $this->header = [];
                foreach ($extend['request']['header'] as $hk => $hv) {
                    $this->header[str_replace('-', '_', $hk)] = $hv;
                }
                $this->cookie = $extend['request']['cookie'];
                $this->method = 'STREAM';
                $this->user_agent = $this->header['user_agent'];
                $this->content_type = !empty($this->header['content_type']) ? strtolower(explode(';', $this->header['content_type'])[0]) : null;
                $this->file = Parse::fileData($extend['request']['files']);
                $rawData = $extend['request']['rawData'];
                break;
            case BootType::SWOOLE_TCP:
                $extend = $this->cargo->getExtend();
                $this->header = [
                    'client_id' => $this->cargo->getBootType() . '#' . $extend['server']->worker_id,
                ];
                $this->cookie = [];
                $this->method = 'STREAM';
                $this->user_agent = $this->header['client_id'];
                $this->content_type = 'application/json';
                $rawData = $extend['request']['rawData'] ?? '';
                break;
            case BootType::SWOOLE_UDP:
                break;
            case BootType::WORKERMAN_HTTP:
                $extend = $this->cargo->getExtend();
                $server = $extend['request']['server'];
                $this->header = [
                    'x_real_ip' => $extend['connection']->getRemoteIp(),
                ];
                foreach ($server as $hk => $hv) {
                    $hk = strtolower($hk);
                    $server[$hk] = $hv;
                    if (strpos($hk, 'http_') === 0) {
                        $this->header[str_replace('http_', '', $hk)] = $hv;
                    }
                }
                $this->cookie = $extend['request']['cookie'];
                $this->method = strtoupper($server['request_method']);
                $this->user_agent = $this->header['user_agent'];
                $this->content_type = !empty($this->header['content_type']) ? strtolower(explode(';', $this->header['content_type'])[0]) : null;
                $this->file = Parse::fileData($extend['request']['files']);
                $_GET = $extend['request']['get'] ?? [];
                $_POST = $extend['request']['post'] ?? [];
                $rawData = $GLOBALS['HTTP_RAW_POST_DATA'];
                break;
            case BootType::WORKERMAN_WEB_SOCKET:
            case BootType::WORKERMAN_TCP:
            case BootType::WORKERMAN_UDP:
                $extend = $this->cargo->getExtend();
                $this->header = [
                    'x_real_ip' => $extend['connection']->getRemoteIp(),
                    'x_host' => $extend['connection']->getRemoteIp() . ":" . $extend['connection']->getRemotePort(),
                    'client_id' => $this->cargo->getBootType() . '#' . $extend['connection']->worker_id,
                ];
                $this->cookie = [];
                $this->method = 'STREAM';
                $this->user_agent = $this->header['client_id'];
                $this->content_type = 'application/json';
                $rawData = $extend['request']['rawData'] ?? '';
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
            $this->ip = '0.0.0.0';
        }
        // SSL
        if ($this->ssl === false && ($server['request_scheme'] ?? '') === 'https') {
            $this->ssl = true;
        }
        if ($this->ssl === false && strpos(($server['server_protocol'] ?? ''), 'https') !== false) {
            $this->ssl = true;
        }
        // HOST / PORT
        $this->host = $this->header['x_host'] ?? $this->header['host'] ?? $server['server_name'] ?? null;
        if ($this->host) {
            $this->port = explode(':', $this->host);
            $this->port = $this->port[1] ?? 80;
            if (strpos($this->host, ':') === false || strpos($this->host, ':') > 6) {
                $this->host = ($this->ssl ? 'https' : 'http') . '://' . $this->host;
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
            case 'STREAM':
                $this->raw = $rawData;
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