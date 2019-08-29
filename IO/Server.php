<?php
/**
 * Server
 */

namespace Yonna\IO;


/**
 * Class Server
 * @package Core\Core\IO
 */
class Server
{

    private $php_self;
    private $gateway_interface;
    private $server_addr;
    private $server_name;
    private $server_software;
    private $server_protocol;
    private $request_method;
    private $request_time;
    private $request_time_float;
    private $query_string;
    private $document_root;
    private $http_accept;
    private $http_accept_charset;
    private $http_accept_encoding;
    private $http_accept_language;
    private $http_connection;
    private $http_host;
    private $http_referer;
    private $http_user_agent;
    private $https;
    private $remote_addr;
    private $remote_host;
    private $remote_port;
    private $remote_user;
    private $redirect_remote_user;
    private $script_filename;
    private $server_admin;
    private $server_port;
    private $server_signature;
    private $path_translated;
    private $script_name;
    private $request_uri;
    private $php_auth_digest;
    private $php_auth_user;
    private $php_auth_pw;
    private $auth_type;
    private $path_info;

}