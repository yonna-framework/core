<?php

/**
 * Request Fields
 */

namespace Yonna\IO;


use SimpleXMLElement;
use Yonna\Bootstrap\Cargo;
use Yonna\Foundation\Parse;

/**
 * Class RequestFields
 * @package Yonna\IO
 */
class RequestBuilder
{

    /**
     * @var null | \swoole_websocket_server | \swoole_http_server | \swoole_server
     */
    protected $swoole = null;

    /**
     * @var null | \Workerman\Connection\TcpConnection  | \Workerman\Connection\UdpConnection
     */
    protected $workerman = null;

    /**
     * @var Cargo
     */
    protected $cargo = null;

    /**
     * origin set
     */
    protected $get = [];
    protected $post = [];
    protected $request = [];
    protected $files = [];
    protected $cookie = [];
    protected $session = [];

    protected $content_length = 0;
    protected $content_type = '';
    protected $php_self = '';
    protected $gateway_interface = '';
    protected $server_addr = '';
    protected $server_name = '';
    protected $server_software = '';
    protected $server_protocol = '';
    protected $request_scheme = '';
    protected $request_method = '';
    protected $request_time = '';
    protected $request_time_float = '';
    protected $query_string = '';
    protected $document_root = '';
    protected $http_accept = '';
    protected $http_accept_charset = '';
    protected $http_accept_encoding = '';
    protected $http_accept_language = '';
    protected $http_connection = '';
    protected $http_host = '';
    protected $http_referer = '';
    protected $http_user_agent = '';
    protected $http_upgrade = '';
    protected $http_origin = '';
    protected $http_sec_websocket_version = '';
    protected $http_sec_websocket_key = '';
    protected $http_sec_websocket_extensions = '';
    protected $https = '';
    protected $remote_addr = '';
    protected $remote_host = '';
    protected $remote_port = '';
    protected $remote_user = '';
    protected $redirect_remote_user = '';
    protected $script_filename = '';
    protected $server_admin = '';
    protected $server_port = '';
    protected $server_signature = '';
    protected $path_translated = '';
    protected $script_name = '';
    protected $request_uri = '';
    protected $php_auth_digest = '';
    protected $php_auth_user = '';
    protected $php_auth_pw = '';
    protected $auth_type = '';
    protected $orig_path_info = '';
    protected $path_info = '';
    protected $http_client_ip = '';

    /**
     * x set
     */
    protected $http_x_real_ip = '';
    protected $http_x_forwarded_for = '';
    protected $http_x_host = '';

    /**
     * extent set
     */
    protected $raw_data = '';
    protected $input = [];
    protected $input_type = InputType::UN_KNOW;
    protected $ip = '0.0.0.0';
    protected $port = 80;
    protected $client_id = '';
    protected $host = '';
    protected $local = false;
    protected $ssl = false;
    protected $scope = '';
    protected $stack = '';

    /**
     * 分析当前的参数并设置额外参数
     */
    private function analysisExtentSet()
    {
        // IP address
        $ip = $this->getHttpXRealIp()
            ?: $this->getHttpXForwardedFor()
                ?: $this->getRemoteAddr()
                    ?: $this->getHttpClientIp();
        if ($ip) {
            $this->setIp($ip);
        }
        if ($this->getIp() === '127.0.0.1') {
            $this->setLocal(true);
        }

        // SSL
        if ($this->getRequestScheme() === 'https' || strpos(($this->getServerProtocol()), 'https') !== false) {
            $this->setSsl(true);
        }

        // HOST / PORT
        $host = $this->getHttpXHost()
            ?: $this->getHttpHost()
                ?: $this->getServerName();
        if ($host) {
            $portExplode = explode(':', $host);
            $port = $portExplode[1] ?? 80;
            if (strpos($host, ':') === false || strpos($host, ':') > 6) {
                $host = ($this->isSsl() ? 'https' : 'http') . '://' . $host;
            }
            $this->setHost($host);
            $this->setPort($port);
        }

        // 处理协议，将可能的数据转为json字符串记录在input
        $data = null;
        switch ($this->getRequestMethod()) {
            case 'GET':
                switch ($this->content_type) {
                    case null:
                        $data = $this->getGet();
                        break;
                    case 'application/x-www-form-urlencoded':
                        parse_str($this->getRawData(), $temp);
                        $data = $temp;
                        break;
                    case 'application/xml':
                    case 'text/xml':
                        $data = simplexml_load_string($this->getRawData());
                        break;
                    case 'text/plain':
                    case 'application/json':
                    default:
                        $data = $this->getRawData();
                        break;
                }
                break;
            case 'POST':
                switch ($this->content_type) {
                    case null:
                    case 'multipart/form-data':
                    case 'application/x-www-form-urlencoded':
                        $data = $this->getPost();
                        break;
                    case 'application/xml':
                    case 'text/xml':
                        $data = simplexml_load_string($this->getRawData());
                        break;
                    case 'text/plain':
                    case 'application/json':
                    default:
                        $data = $this->getRawData();
                        break;
                }
                break;
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                switch ($this->content_type) {
                    case 'application/xml':
                    case 'text/xml':
                        $data = simplexml_load_string($this->getRawData());
                        break;
                    case 'text/plain':
                    case 'application/json':
                    default:
                        $data = $this->getRawData();
                        break;
                }
                break;
            case 'STREAM':
            default:
                $data = $this->getRawData();
                break;
        }
        if ($data instanceof SimpleXMLElement) {
            $this->setInputType(InputType::XML);
            $this->setRawData(json_encode($data));
        } else if (is_array($data)) {
            $this->setInputType(InputType::FORM);
            $this->setRawData(json_encode($data));
        } else {
            $this->setInputType(InputType::RAW);
        }
    }

    /**
     * register global request
     * 读取全局变量
     */
    protected function loadGlobal()
    {
        $raw = file_get_contents('php://input');
        !$raw && $raw = $GLOBALS['HTTP_RAW_POST_DATA'] ?? '';
        $_SERVER['CONTENT_TYPE'] = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($_SERVER['CONTENT_TYPE'], ';') !== false) {
            $_SERVER['CONTENT_TYPE'] = explode(';', $_SERVER['CONTENT_TYPE']);
            $_SERVER['CONTENT_TYPE'] = current($_SERVER['CONTENT_TYPE']);
        }
        $this->setGet($_GET ?? []);
        $this->setPost($_POST ?? []);
        $this->setRequest($_REQUEST ?? []);
        $this->setFiles($_FILES ?? []);
        $this->setCookie($_COOKIE ?? []);
        $this->setSession($_SESSION ?? []);
        $this->setRawData($raw);
        $this->setContentLength(intval($_SERVER['CONTENT_LENGTH'] ?? 0));
        $this->setContentType($_SERVER['CONTENT_TYPE'] ?? '');
        $this->setPhpSelf($_SERVER['PHP_SELF'] ?? '');
        $this->setGatewayInterface($_SERVER['GATEWAY_INTERFACE'] ?? '');
        $this->setRequestMethod($_SERVER['REQUEST_METHOD'] ?? '');
        $this->setRequestTime($_SERVER['REQUEST_TIME'] ?? '');
        $this->setRequestTimeFloat($_SERVER['REQUEST_TIME_FLOAT'] ?? '');
        $this->setRequestScheme($_SERVER['REQUEST_SCHEME'] ?? '');
        $this->setQueryString($_SERVER['QUERY_STRING'] ?? '');
        $this->setDocumentRoot($_SERVER['DOCUMENT_ROOT'] ?? '');
        $this->setHttpAccept($_SERVER['HTTP_ACCEPT'] ?? '');
        $this->setHttpAcceptCharset($_SERVER['HTTP_ACCEPT_CHARSET'] ?? '');
        $this->setHttpAcceptEncoding($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '');
        $this->setHttpAcceptLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
        $this->setHttpConnection($_SERVER['HTTP_CONNECTION'] ?? '');
        $this->setHttpHost($_SERVER['HTTP_HOST'] ?? '');
        $this->setHttpReferer($_SERVER['HTTP_REFERER'] ?? '');
        $this->setHttpUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '');
        $this->setHttpUpgrade($_SERVER['HTTP_UPGRADE'] ?? '');
        $this->setHttpOrigin($_SERVER['HTTP_ORIGIN'] ?? '');
        $this->setHttps($_SERVER['HTTPS'] ?? '');
        $this->setRemoteAddr($_SERVER['REMOTE_ADDR'] ?? '');
        $this->setRemoteHost($_SERVER['REMOTE_HOST'] ?? '');
        $this->setRemotePort($_SERVER['REMOTE_PORT'] ?? '');
        $this->setRemoteUser($_SERVER['REMOTE_USER'] ?? '');
        $this->setRedirectRemoteUser($_SERVER['REDIRECT_REMOTE_USER'] ?? '');
        $this->setScriptFilename($_SERVER['SCRIPT_FILENAME'] ?? '');
        $this->setServerAddr($_SERVER['SERVER_ADDR'] ?? '');
        $this->setServerName($_SERVER['SERVER_NAME'] ?? '');
        $this->setServerSoftware($_SERVER['SERVER_SOFTWARE'] ?? '');
        $this->setServerProtocol($_SERVER['SERVER_PROTOCOL'] ?? '');
        $this->setServerAdmin($_SERVER['SERVER_ADMIN'] ?? '');
        $this->setServerPort($_SERVER['SERVER_PORT'] ?? '');
        $this->setServerSignature($_SERVER['SERVER_SIGNATURE'] ?? '');
        $this->setPathTranslated($_SERVER['PATH_TRANSLATED'] ?? '');
        $this->setScriptName($_SERVER['SCRIPT_NAME'] ?? '');
        $this->setRequestUri($_SERVER['REQUEST_URI'] ?? '');
        $this->setPhpAuthDigest($_SERVER['PHP_AUTH_DIGEST'] ?? '');
        $this->setPhpAuthUser($_SERVER['PHP_AUTH_USER'] ?? '');
        $this->setPhpAuthPw($_SERVER['PHP_AUTH_PW'] ?? '');
        $this->setAuthType($_SERVER['AUTH_TYPE'] ?? '');
        $this->setOrigPathInfo($_SERVER['ORIG_PATH_INFO'] ?? '');
        $this->setPathInfo($_SERVER['PATH_INFO'] ?? '');
        $this->setHttpClientIp($_SERVER['HTTP_CLIENT_IP'] ?? '');

        $this->setHttpXRealIp($_SERVER['HTTP_X_REAL_IP'] ?? '');
        $this->setHttpXHost($_SERVER['HTTP_X_HOST'] ?? '');
        $this->setHttpXForwardedFor($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');

        $this->analysisExtentSet();
    }

    /**
     * 读取拓展 RequestBuilder
     * @param RequestBuilder $requestBuilder
     */
    protected function loadRequestBuilder(RequestBuilder $requestBuilder)
    {
        $keys = [
            'Swoole',
            'Workerman',
            'Get',
            'Post',
            'Request',
            'Files',
            'Cookie',
            'Session',
            'RawData',
            'ContentLength',
            'ContentType',
            'PhpSelf',
            'GatewayInterface',
            'RequestMethod',
            'RequestTime',
            'RequestTimeFloat',
            'RequestScheme',
            'QueryString',
            'DocumentRoot',
            'HttpAccept',
            'HttpAcceptCharset',
            'HttpAcceptEncoding',
            'HttpAcceptLanguage',
            'HttpConnection',
            'HttpHost',
            'HttpReferer',
            'HttpUserAgent',
            'HttpUpgrade',
            'HttpOrigin',
            'HttpSecWebsocketVersion',
            'HttpSecWebsocketKey',
            'HttpSecWebsocketExtensions',
            'Https',
            'RemoteAddr',
            'RemoteHost',
            'RemotePort',
            'RemoteUser',
            'RedirectRemoteUser',
            'ScriptFilename',
            'ServerAddr',
            'ServerName',
            'ServerSoftware',
            'ServerProtocol',
            'ServerAdmin',
            'ServerPort',
            'ServerSignature',
            'PathTranslated',
            'ScriptName',
            'RequestUri',
            'PhpAuthDigest',
            'PhpAuthUser',
            'PhpAuthPw',
            'AccountType',
            'OrigPathInfo',
            'PathInfo',
            'HttpClientIp',
            'HttpXRealIp',
            'HttpXHost',
            'HttpXForwardedFor',
        ];
        foreach ($keys as $k) {
            $get = "get{$k}";
            $set = "set{$k}";
            if (!$this->$get() && $requestBuilder->$get()) {
                $this->$set($requestBuilder->$get());
            }
        }
        $this->analysisExtentSet();
    }

    /**
     * @return \swoole_http_server|\swoole_server|\swoole_websocket_server|null
     */
    public function getSwoole()
    {
        return $this->swoole;
    }

    /**
     * @param \swoole_http_server|\swoole_server|\swoole_websocket_server|null $swoole
     */
    public function setSwoole($swoole): void
    {
        $this->swoole = $swoole;
    }

    /**
     * @return \Workerman\Connection\TcpConnection|\Workerman\Connection\UdpConnection|null
     */
    public function getWorkerman()
    {
        return $this->workerman;
    }

    /**
     * @param \Workerman\Connection\TcpConnection|\Workerman\Connection\UdpConnection|null $workerman
     */
    public function setWorkerman($workerman): void
    {
        $this->workerman = $workerman;
    }

    /**
     * @return int
     */
    public function getContentLength(): int
    {
        return $this->content_length;
    }

    /**
     * @param int $content_length
     */
    public function setContentLength(int $content_length): void
    {
        $this->content_length = $content_length;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->content_type;
    }

    /**
     * @param string $content_type
     */
    public function setContentType(string $content_type): void
    {
        $this->content_type = $content_type;
    }

    /**
     * @return mixed
     */
    public function getPhpSelf()
    {
        return $this->php_self;
    }

    /**
     * @param mixed $php_self
     */
    public function setPhpSelf($php_self): void
    {
        $this->php_self = $php_self;
    }

    /**
     * @return string
     */
    public function getGatewayInterface(): string
    {
        return $this->gateway_interface;
    }

    /**
     * @param string $gateway_interface
     */
    public function setGatewayInterface(string $gateway_interface): void
    {
        $this->gateway_interface = $gateway_interface;
    }

    /**
     * @return string
     */
    public function getServerAddr(): string
    {
        return $this->server_addr;
    }

    /**
     * @param string $server_addr
     */
    public function setServerAddr(string $server_addr): void
    {
        $this->server_addr = $server_addr;
    }

    /**
     * @return string
     */
    public function getServerName(): string
    {
        return $this->server_name;
    }

    /**
     * @param string $server_name
     */
    public function setServerName(string $server_name): void
    {
        $this->server_name = $server_name;
    }

    /**
     * @return string
     */
    public function getServerSoftware(): string
    {
        return $this->server_software;
    }

    /**
     * @param string $server_software
     */
    public function setServerSoftware(string $server_software): void
    {
        $this->server_software = $server_software;
    }

    /**
     * @return string
     */
    public function getServerProtocol(): string
    {
        return $this->server_protocol;
    }

    /**
     * @param string $server_protocol
     */
    public function setServerProtocol(string $server_protocol): void
    {
        $this->server_protocol = $server_protocol;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->request_method;
    }

    /**
     * @param string $request_method
     */
    public function setRequestMethod(string $request_method): void
    {
        $this->request_method = $request_method ? strtoupper($request_method) : '';
    }

    /**
     * @return string
     */
    public function getRequestTime(): string
    {
        return $this->request_time;
    }

    /**
     * @param string $request_time
     */
    public function setRequestTime(string $request_time): void
    {
        $this->request_time = $request_time;
    }

    /**
     * @return string
     */
    public function getRequestTimeFloat(): string
    {
        return $this->request_time_float;
    }

    /**
     * @param string $request_time_float
     */
    public function setRequestTimeFloat(string $request_time_float): void
    {
        $this->request_time_float = $request_time_float;
    }

    /**
     * @return string
     */
    public function getRequestScheme(): string
    {
        return $this->request_scheme;
    }

    /**
     * @param string $request_scheme
     */
    public function setRequestScheme(string $request_scheme): void
    {
        $this->request_scheme = $request_scheme;
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->query_string;
    }

    /**
     * @param string $query_string
     */
    public function setQueryString(string $query_string): void
    {
        $this->query_string = $query_string;
    }

    /**
     * @return string
     */
    public function getDocumentRoot(): string
    {
        return $this->document_root;
    }

    /**
     * @param string $document_root
     */
    public function setDocumentRoot(string $document_root): void
    {
        $this->document_root = $document_root;
    }

    /**
     * @return string
     */
    public function getHttpAccept(): string
    {
        return $this->http_accept;
    }

    /**
     * @param string $http_accept
     */
    public function setHttpAccept(string $http_accept): void
    {
        $this->http_accept = $http_accept;
    }

    /**
     * @return string
     */
    public function getHttpAcceptCharset(): string
    {
        return $this->http_accept_charset;
    }

    /**
     * @param string $http_accept_charset
     */
    public function setHttpAcceptCharset(string $http_accept_charset): void
    {
        $this->http_accept_charset = $http_accept_charset;
    }

    /**
     * @return string
     */
    public function getHttpAcceptEncoding(): string
    {
        return $this->http_accept_encoding;
    }

    /**
     * @param string $http_accept_encoding
     */
    public function setHttpAcceptEncoding(string $http_accept_encoding): void
    {
        $this->http_accept_encoding = $http_accept_encoding;
    }

    /**
     * @return string
     */
    public function getHttpAcceptLanguage(): string
    {
        return $this->http_accept_language;
    }

    /**
     * @param string $http_accept_language
     */
    public function setHttpAcceptLanguage(string $http_accept_language): void
    {
        $this->http_accept_language = $http_accept_language;
    }

    /**
     * @return string
     */
    public function getHttpConnection(): string
    {
        return $this->http_connection;
    }

    /**
     * @param string $http_connection
     */
    public function setHttpConnection(string $http_connection): void
    {
        $this->http_connection = $http_connection;
    }

    /**
     * @return string
     */
    public function getHttpHost(): string
    {
        return $this->http_host;
    }

    /**
     * @param string $http_host
     */
    public function setHttpHost(string $http_host): void
    {
        $this->http_host = $http_host;
    }

    /**
     * @return string
     */
    public function getHttpReferer(): string
    {
        return $this->http_referer;
    }

    /**
     * @param string $http_referer
     */
    public function setHttpReferer(string $http_referer): void
    {
        $this->http_referer = $http_referer;
    }

    /**
     * @return string
     */
    public function getHttpUserAgent(): string
    {
        return $this->http_user_agent;
    }

    /**
     * @param string $http_user_agent
     */
    public function setHttpUserAgent(string $http_user_agent): void
    {
        $this->http_user_agent = $http_user_agent;
    }

    /**
     * @return string
     */
    public function getHttpUpgrade(): string
    {
        return $this->http_upgrade;
    }

    /**
     * @param string $http_upgrade
     */
    public function setHttpUpgrade(string $http_upgrade): void
    {
        $this->http_upgrade = $http_upgrade;
    }

    /**
     * @return string
     */
    public function getHttpOrigin(): string
    {
        return $this->http_origin;
    }

    /**
     * @param string $http_origin
     */
    public function setHttpOrigin(string $http_origin): void
    {
        $this->http_origin = $http_origin;
    }

    /**
     * @return string
     */
    public function getHttpSecWebsocketVersion(): string
    {
        return $this->http_sec_websocket_version;
    }

    /**
     * @param string $http_sec_websocket_version
     */
    public function setHttpSecWebsocketVersion(string $http_sec_websocket_version): void
    {
        $this->http_sec_websocket_version = $http_sec_websocket_version;
    }

    /**
     * @return string
     */
    public function getHttpSecWebsocketKey(): string
    {
        return $this->http_sec_websocket_key;
    }

    /**
     * @param string $http_sec_websocket_key
     */
    public function setHttpSecWebsocketKey(string $http_sec_websocket_key): void
    {
        $this->http_sec_websocket_key = $http_sec_websocket_key;
    }

    /**
     * @return string
     */
    public function getHttpSecWebsocketExtensions(): string
    {
        return $this->http_sec_websocket_extensions;
    }

    /**
     * @param string $http_sec_websocket_extensions
     */
    public function setHttpSecWebsocketExtensions(string $http_sec_websocket_extensions): void
    {
        $this->http_sec_websocket_extensions = $http_sec_websocket_extensions;
    }

    /**
     * @return string
     */
    public function getHttps(): string
    {
        return $this->https;
    }

    /**
     * @param string $https
     */
    public function setHttps(string $https): void
    {
        $this->https = $https;
    }

    /**
     * @return string
     */
    public function getRemoteAddr(): string
    {
        return $this->remote_addr;
    }

    /**
     * @param string $remote_addr
     */
    public function setRemoteAddr(string $remote_addr): void
    {
        $this->remote_addr = $remote_addr;
    }

    /**
     * @return string
     */
    public function getRemoteHost(): string
    {
        return $this->remote_host;
    }

    /**
     * @param string $remote_host
     */
    public function setRemoteHost(string $remote_host): void
    {
        $this->remote_host = $remote_host;
    }

    /**
     * @return string
     */
    public function getRemotePort(): string
    {
        return $this->remote_port;
    }

    /**
     * @param string $remote_port
     */
    public function setRemotePort(string $remote_port): void
    {
        $this->remote_port = $remote_port;
    }

    /**
     * @return string
     */
    public function getRemoteUser(): string
    {
        return $this->remote_user;
    }

    /**
     * @param string $remote_user
     */
    public function setRemoteUser(string $remote_user): void
    {
        $this->remote_user = $remote_user;
    }

    /**
     * @return string
     */
    public function getRedirectRemoteUser(): string
    {
        return $this->redirect_remote_user;
    }

    /**
     * @param string $redirect_remote_user
     */
    public function setRedirectRemoteUser(string $redirect_remote_user): void
    {
        $this->redirect_remote_user = $redirect_remote_user;
    }

    /**
     * @return string
     */
    public function getScriptFilename(): string
    {
        return $this->script_filename;
    }

    /**
     * @param string $script_filename
     */
    public function setScriptFilename(string $script_filename): void
    {
        $this->script_filename = $script_filename;
    }

    /**
     * @return string
     */
    public function getServerAdmin(): string
    {
        return $this->server_admin;
    }

    /**
     * @param string $server_admin
     */
    public function setServerAdmin(string $server_admin): void
    {
        $this->server_admin = $server_admin;
    }

    /**
     * @return string
     */
    public function getServerPort(): string
    {
        return $this->server_port;
    }

    /**
     * @param string $server_port
     */
    public function setServerPort(string $server_port): void
    {
        $this->server_port = $server_port;
    }

    /**
     * @return string
     */
    public function getServerSignature(): string
    {
        return $this->server_signature;
    }

    /**
     * @param string $server_signature
     */
    public function setServerSignature(string $server_signature): void
    {
        $this->server_signature = $server_signature;
    }

    /**
     * @return string
     */
    public function getPathTranslated(): string
    {
        return $this->path_translated;
    }

    /**
     * @param string $path_translated
     */
    public function setPathTranslated(string $path_translated): void
    {
        $this->path_translated = $path_translated;
    }

    /**
     * @return string
     */
    public function getScriptName(): string
    {
        return $this->script_name;
    }

    /**
     * @param string $script_name
     */
    public function setScriptName(string $script_name): void
    {
        $this->script_name = $script_name;
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->request_uri;
    }

    /**
     * @param string $request_uri
     */
    public function setRequestUri(string $request_uri): void
    {
        $this->request_uri = $request_uri;
    }

    /**
     * @return string
     */
    public function getPhpAuthDigest(): string
    {
        return $this->php_auth_digest;
    }

    /**
     * @param string $php_auth_digest
     */
    public function setPhpAuthDigest(string $php_auth_digest): void
    {
        $this->php_auth_digest = $php_auth_digest;
    }

    /**
     * @return string
     */
    public function getPhpAuthUser(): string
    {
        return $this->php_auth_user;
    }

    /**
     * @param string $php_auth_user
     */
    public function setPhpAuthUser(string $php_auth_user): void
    {
        $this->php_auth_user = $php_auth_user;
    }

    /**
     * @return string
     */
    public function getPhpAuthPw(): string
    {
        return $this->php_auth_pw;
    }

    /**
     * @param string $php_auth_pw
     */
    public function setPhpAuthPw(string $php_auth_pw): void
    {
        $this->php_auth_pw = $php_auth_pw;
    }

    /**
     * @return string
     */
    public function getAuthType(): string
    {
        return $this->auth_type;
    }

    /**
     * @param string $auth_type
     */
    public function setAuthType(string $auth_type): void
    {
        $this->auth_type = $auth_type;
    }

    /**
     * @return string
     */
    public function getOrigPathInfo(): string
    {
        return $this->orig_path_info;
    }

    /**
     * @param string $orig_path_info
     */
    public function setOrigPathInfo(string $orig_path_info): void
    {
        $this->orig_path_info = $orig_path_info;
    }

    /**
     * @return string
     */
    public function getPathInfo(): string
    {
        return $this->path_info;
    }

    /**
     * @param string $path_info
     */
    public function setPathInfo(string $path_info): void
    {
        $this->path_info = $path_info;
    }

    /**
     * @return array
     */
    public function getGet(): array
    {
        return $this->get;
    }

    /**
     * @param array $get
     */
    public function setGet(array $get): void
    {
        $this->get = $get;
    }

    /**
     * @return array
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @param array $post
     */
    public function setPost(array $post): void
    {
        $this->post = $post;
    }

    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    /**
     * @param array $request
     */
    public function setRequest(array $request): void
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files ? Parse::fileData($files) : [];
    }

    /**
     * @return array
     */
    public function getCookie(): array
    {
        return $this->cookie;
    }

    /**
     * @param array $cookie
     */
    public function setCookie(array $cookie): void
    {
        $this->cookie = $cookie;
    }

    /**
     * @return array
     */
    public function getSession(): array
    {
        return $this->session;
    }

    /**
     * @param array $session
     */
    public function setSession(array $session): void
    {
        $this->session = $session;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->raw_data;
    }

    /**
     * @param mixed $raw_data
     */
    public function setRawData($raw_data): void
    {
        $this->raw_data = $raw_data;
    }

    /**
     * @return array
     */
    public function getInput(): array
    {
        return $this->input;
    }

    /**
     * @param array $input
     */
    public function setInput(array $input): void
    {
        $this->input = $input;
    }

    /**
     * @return string
     */
    public function getInputType(): string
    {
        return $this->input_type;
    }

    /**
     * @param string $input_type
     */
    public function setInputType(string $input_type): void
    {
        $this->input_type = $input_type;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->client_id;
    }

    /**
     * @param string $client_id
     */
    public function setClientId(string $client_id): void
    {
        $this->client_id = $client_id;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return bool
     */
    public function isSsl(): bool
    {
        return $this->ssl;
    }

    /**
     * @param bool $ssl
     */
    public function setSsl(bool $ssl): void
    {
        $this->ssl = $ssl;
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
     * @return string
     */
    public function getStack(): string
    {
        return $this->stack;
    }

    /**
     * @param string $stack
     */
    public function setStack(string $stack): void
    {
        $this->stack = $stack;
    }

    /**
     * @return mixed
     */
    public function getHttpXRealIp()
    {
        return $this->http_x_real_ip;
    }

    /**
     * @param mixed $http_x_real_ip
     */
    public function setHttpXRealIp($http_x_real_ip): void
    {
        $this->http_x_real_ip = $http_x_real_ip;
    }

    /**
     * @return string
     */
    public function getHttpClientIp(): string
    {
        return $this->http_client_ip;
    }

    /**
     * @param string $http_client_ip
     */
    public function setHttpClientIp(string $http_client_ip): void
    {
        $this->http_client_ip = $http_client_ip;
    }

    /**
     * @return string
     */
    public function getHttpXForwardedFor(): string
    {
        return $this->http_x_forwarded_for;
    }

    /**
     * @param string $http_x_forwarded_for
     */
    public function setHttpXForwardedFor(string $http_x_forwarded_for): void
    {
        $this->http_x_forwarded_for = $http_x_forwarded_for;
    }

    /**
     * @return string
     */
    public function getHttpXHost(): string
    {
        return $this->http_x_host;
    }

    /**
     * @param string $http_x_host
     */
    public function setHttpXHost(string $http_x_host): void
    {
        $this->http_x_host = $http_x_host;
    }

    /**
     * @return bool
     */
    public function isLocal(): bool
    {
        return $this->local;
    }

    /**
     * @param bool $local
     */
    public function setLocal(bool $local): void
    {
        $this->local = $local;
    }


}