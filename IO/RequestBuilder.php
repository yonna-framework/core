<?php

/**
 * Request Fields
 */

namespace Yonna\IO;


use Yonna\Bootstrap\Cargo;

/**
 * Class RequestFields
 * @package Yonna\IO
 */
class RequestBuilder
{

    /**
     * @var Cargo
     */
    protected $cargo = null;

    protected $php_self;
    protected $gateway_interface;
    protected $server_addr;
    protected $server_name;
    protected $server_software;
    protected $server_protocol;
    protected $request_method;
    protected $request_time;
    protected $request_time_float;
    protected $query_string;
    protected $document_root;
    protected $http_accept;
    protected $http_accept_charset;
    protected $http_accept_encoding;
    protected $http_accept_language;
    protected $http_connection;
    protected $http_host;
    protected $http_referer;
    protected $http_user_agent;
    protected $https;
    protected $remote_addr;
    protected $remote_host;
    protected $remote_port;
    protected $remote_user;
    protected $redirect_remote_user;
    protected $script_filename;
    protected $server_admin;
    protected $server_port;
    protected $server_signature;
    protected $path_translated;
    protected $script_name;
    protected $request_uri;
    protected $php_auth_digest;
    protected $php_auth_user;
    protected $php_auth_pw;
    protected $auth_type;
    protected $path_info;

    protected $get;
    protected $post;
    protected $files;
    protected $request;
    protected $session;
    protected $cookie;

    protected $raw_data;
    protected $input = '';
    protected $input_type = InputType::UN_KNOW;
    protected $ip = '0.0.0.0';
    protected $port = 80;
    protected $client_id = null;
    protected $host = null;
    protected $ssl = false;
    protected $scope = '';
    protected $stack = '';

    /**
     * @param mixed $php_self
     */
    public function setPhpSelf($php_self): void
    {
        $this->php_self = $php_self;
    }

    /**
     * @param mixed $gateway_interface
     */
    public function setGatewayInterface($gateway_interface): void
    {
        $this->gateway_interface = $gateway_interface;
    }

    /**
     * @param mixed $server_addr
     */
    public function setServerAddr($server_addr): void
    {
        $this->server_addr = $server_addr;
    }

    /**
     * @param mixed $server_name
     */
    public function setServerName($server_name): void
    {
        $this->server_name = $server_name;
    }

    /**
     * @param mixed $server_software
     */
    public function setServerSoftware($server_software): void
    {
        $this->server_software = $server_software;
    }

    /**
     * @param mixed $server_protocol
     */
    public function setServerProtocol($server_protocol): void
    {
        $this->server_protocol = $server_protocol;
    }

    /**
     * @param mixed $request_method
     */
    public function setRequestMethod($request_method): void
    {
        $this->request_method = $request_method;
    }

    /**
     * @param mixed $request_time
     */
    public function setRequestTime($request_time): void
    {
        $this->request_time = $request_time;
    }

    /**
     * @param mixed $request_time_float
     */
    public function setRequestTimeFloat($request_time_float): void
    {
        $this->request_time_float = $request_time_float;
    }

    /**
     * @param mixed $query_string
     */
    public function setQueryString($query_string): void
    {
        $this->query_string = $query_string;
    }

    /**
     * @param mixed $document_root
     */
    public function setDocumentRoot($document_root): void
    {
        $this->document_root = $document_root;
    }

    /**
     * @param mixed $http_accept
     */
    public function setHttpAccept($http_accept): void
    {
        $this->http_accept = $http_accept;
    }

    /**
     * @param mixed $http_accept_charset
     */
    public function setHttpAcceptCharset($http_accept_charset): void
    {
        $this->http_accept_charset = $http_accept_charset;
    }

    /**
     * @param mixed $http_accept_encoding
     */
    public function setHttpAcceptEncoding($http_accept_encoding): void
    {
        $this->http_accept_encoding = $http_accept_encoding;
    }

    /**
     * @param mixed $http_accept_language
     */
    public function setHttpAcceptLanguage($http_accept_language): void
    {
        $this->http_accept_language = $http_accept_language;
    }

    /**
     * @param mixed $http_connection
     */
    public function setHttpConnection($http_connection): void
    {
        $this->http_connection = $http_connection;
    }

    /**
     * @param mixed $http_host
     */
    public function setHttpHost($http_host): void
    {
        $this->http_host = $http_host;
    }

    /**
     * @param mixed $http_referer
     */
    public function setHttpReferer($http_referer): void
    {
        $this->http_referer = $http_referer;
    }

    /**
     * @param mixed $http_user_agent
     */
    public function setHttpUserAgent($http_user_agent): void
    {
        $this->http_user_agent = $http_user_agent;
    }

    /**
     * @param mixed $https
     */
    public function setHttps($https): void
    {
        $this->https = $https;
    }

    /**
     * @param mixed $remote_addr
     */
    public function setRemoteAddr($remote_addr): void
    {
        $this->remote_addr = $remote_addr;
    }

    /**
     * @param mixed $remote_host
     */
    public function setRemoteHost($remote_host): void
    {
        $this->remote_host = $remote_host;
    }

    /**
     * @param mixed $remote_port
     */
    public function setRemotePort($remote_port): void
    {
        $this->remote_port = $remote_port;
    }

    /**
     * @param mixed $remote_user
     */
    public function setRemoteUser($remote_user): void
    {
        $this->remote_user = $remote_user;
    }

    /**
     * @param mixed $redirect_remote_user
     */
    public function setRedirectRemoteUser($redirect_remote_user): void
    {
        $this->redirect_remote_user = $redirect_remote_user;
    }

    /**
     * @param mixed $script_filename
     */
    public function setScriptFilename($script_filename): void
    {
        $this->script_filename = $script_filename;
    }

    /**
     * @param mixed $server_admin
     */
    public function setServerAdmin($server_admin): void
    {
        $this->server_admin = $server_admin;
    }

    /**
     * @param mixed $server_port
     */
    public function setServerPort($server_port): void
    {
        $this->server_port = $server_port;
    }

    /**
     * @param mixed $server_signature
     */
    public function setServerSignature($server_signature): void
    {
        $this->server_signature = $server_signature;
    }

    /**
     * @param mixed $path_translated
     */
    public function setPathTranslated($path_translated): void
    {
        $this->path_translated = $path_translated;
    }

    /**
     * @param mixed $script_name
     */
    public function setScriptName($script_name): void
    {
        $this->script_name = $script_name;
    }

    /**
     * @param mixed $request_uri
     */
    public function setRequestUri($request_uri): void
    {
        $this->request_uri = $request_uri;
    }

    /**
     * @param mixed $php_auth_digest
     */
    public function setPhpAuthDigest($php_auth_digest): void
    {
        $this->php_auth_digest = $php_auth_digest;
    }

    /**
     * @param mixed $php_auth_user
     */
    public function setPhpAuthUser($php_auth_user): void
    {
        $this->php_auth_user = $php_auth_user;
    }

    /**
     * @param mixed $php_auth_pw
     */
    public function setPhpAuthPw($php_auth_pw): void
    {
        $this->php_auth_pw = $php_auth_pw;
    }

    /**
     * @param mixed $auth_type
     */
    public function setAuthType($auth_type): void
    {
        $this->auth_type = $auth_type;
    }

    /**
     * @param mixed $path_info
     */
    public function setPathInfo($path_info): void
    {
        $this->path_info = $path_info;
    }

    /**
     * @param mixed $get
     */
    public function setGet($get): void
    {
        $this->get = $get;
    }

    /**
     * @param mixed $post
     */
    public function setPost($post): void
    {
        $this->post = $post;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files): void
    {
        $this->files = $files;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request): void
    {
        $this->request = $request;
    }

    /**
     * @param mixed $session
     */
    public function setSession($session): void
    {
        $this->session = $session;
    }

    /**
     * @param mixed $cookie
     */
    public function setCookie($cookie): void
    {
        $this->cookie = $cookie;
    }

    /**
     * @param mixed $raw_data
     */
    public function setRawData($raw_data): void
    {
        $this->raw_data = $raw_data;
    }

    /**
     * @param string $input
     */
    public function setInput(string $input): void
    {
        $this->input = $input;
    }

    /**
     * @param string $input_type
     */
    public function setInputType(string $input_type): void
    {
        $this->input_type = $input_type;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @param null $client_id
     */
    public function setClientId($client_id): void
    {
        $this->client_id = $client_id;
    }

    /**
     * @param null $host
     */
    public function setHost($host): void
    {
        $this->host = $host;
    }

    /**
     * @param bool $ssl
     */
    public function setSsl(bool $ssl): void
    {
        $this->ssl = $ssl;
    }

    /**
     * @param string $scope
     */
    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    /**
     * @param string $stack
     */
    public function setStack(string $stack): void
    {
        $this->stack = $stack;
    }

    /**
     * @return Cargo
     */
    public function getCargo(): Cargo
    {
        return $this->cargo;
    }

    /**
     * @return mixed
     */
    public function getPhpSelf()
    {
        return $this->php_self;
    }

    /**
     * @return mixed
     */
    public function getGatewayInterface()
    {
        return $this->gateway_interface;
    }

    /**
     * @return mixed
     */
    public function getServerAddr()
    {
        return $this->server_addr;
    }

    /**
     * @return mixed
     */
    public function getServerName()
    {
        return $this->server_name;
    }

    /**
     * @return mixed
     */
    public function getServerSoftware()
    {
        return $this->server_software;
    }

    /**
     * @return mixed
     */
    public function getServerProtocol()
    {
        return $this->server_protocol;
    }

    /**
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->request_method;
    }

    /**
     * @return mixed
     */
    public function getRequestTime()
    {
        return $this->request_time;
    }

    /**
     * @return mixed
     */
    public function getRequestTimeFloat()
    {
        return $this->request_time_float;
    }

    /**
     * @return mixed
     */
    public function getQueryString()
    {
        return $this->query_string;
    }

    /**
     * @return mixed
     */
    public function getDocumentRoot()
    {
        return $this->document_root;
    }

    /**
     * @return mixed
     */
    public function getHttpAccept()
    {
        return $this->http_accept;
    }

    /**
     * @return mixed
     */
    public function getHttpAcceptCharset()
    {
        return $this->http_accept_charset;
    }

    /**
     * @return mixed
     */
    public function getHttpAcceptEncoding()
    {
        return $this->http_accept_encoding;
    }

    /**
     * @return mixed
     */
    public function getHttpAcceptLanguage()
    {
        return $this->http_accept_language;
    }

    /**
     * @return mixed
     */
    public function getHttpConnection()
    {
        return $this->http_connection;
    }

    /**
     * @return mixed
     */
    public function getHttpHost()
    {
        return $this->http_host;
    }

    /**
     * @return mixed
     */
    public function getHttpReferer()
    {
        return $this->http_referer;
    }

    /**
     * @return mixed
     */
    public function getHttpUserAgent()
    {
        return $this->http_user_agent;
    }

    /**
     * @return mixed
     */
    public function getHttps()
    {
        return $this->https;
    }

    /**
     * @return mixed
     */
    public function getRemoteAddr()
    {
        return $this->remote_addr;
    }

    /**
     * @return mixed
     */
    public function getRemoteHost()
    {
        return $this->remote_host;
    }

    /**
     * @return mixed
     */
    public function getRemotePort()
    {
        return $this->remote_port;
    }

    /**
     * @return mixed
     */
    public function getRemoteUser()
    {
        return $this->remote_user;
    }

    /**
     * @return mixed
     */
    public function getRedirectRemoteUser()
    {
        return $this->redirect_remote_user;
    }

    /**
     * @return mixed
     */
    public function getScriptFilename()
    {
        return $this->script_filename;
    }

    /**
     * @return mixed
     */
    public function getServerAdmin()
    {
        return $this->server_admin;
    }

    /**
     * @return mixed
     */
    public function getServerPort()
    {
        return $this->server_port;
    }

    /**
     * @return mixed
     */
    public function getServerSignature()
    {
        return $this->server_signature;
    }

    /**
     * @return mixed
     */
    public function getPathTranslated()
    {
        return $this->path_translated;
    }

    /**
     * @return mixed
     */
    public function getScriptName()
    {
        return $this->script_name;
    }

    /**
     * @return mixed
     */
    public function getRequestUri()
    {
        return $this->request_uri;
    }

    /**
     * @return mixed
     */
    public function getPhpAuthDigest()
    {
        return $this->php_auth_digest;
    }

    /**
     * @return mixed
     */
    public function getPhpAuthUser()
    {
        return $this->php_auth_user;
    }

    /**
     * @return mixed
     */
    public function getPhpAuthPw()
    {
        return $this->php_auth_pw;
    }

    /**
     * @return mixed
     */
    public function getAuthType()
    {
        return $this->auth_type;
    }

    /**
     * @return mixed
     */
    public function getPathInfo()
    {
        return $this->path_info;
    }

    /**
     * @return mixed
     */
    public function getGet()
    {
        return $this->get;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return mixed
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->raw_data;
    }

    /**
     * @return string
     */
    public function getInput(): string
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
     * @return string
     */
    public function getIp(): string
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
     * @return null
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @return null
     */
    public function getHost()
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
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getStack(): string
    {
        return $this->stack;
    }



}