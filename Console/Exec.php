<?php

namespace Yonna\Console;

use Exception;
use Yonna\Core;

/**
 * yonna 的命令窗体
 * Class Commander
 */
class Exec
{

    const HEAD = '>Yonna<: ';
    const CLS = "\e[H\e[J";

    private static $commands = [
        array('key' => ['cls', 'clear'], 'options' => '', 'desc' => 'clean screen'),
        array('key' => ['ls', 'dir'], 'options' => '', 'desc' => 'explore dir'),
        array('key' => ['die', 'exit'], 'options' => '', 'desc' => 'exit exec'),
        array('key' => ['-h', 'help'], 'options' => '', 'desc' => 'get command list'),
        array('key' => ['pkg'], 'options' => '-c [CONFIG PATH]', 'desc' => 'package project'),
        array('key' => ['swh'], 'options' => '-p [PORT] -e [ENV]', 'desc' => 'start a swoole http server'),
        array('key' => ['swws'], 'options' => '-p [PORT] -e [ENV]', 'desc' => 'start a swoole websocket server'),
        array('key' => ['swt'], 'options' => '-p [PORT] -e [ENV]', 'desc' => 'start a swoole tcp server'),
        array('key' => ['swu'], 'options' => '-p [PORT] -e [ENV]', 'desc' => 'start a swoole udp server'),
        array('key' => ['wmh'], 'options' => '-p [PORT] -e [ENV]', 'desc' => 'start a workerman http server'),
        array('key' => ['wmws'], 'options' => '-p [PORT] -e [ENV]', 'desc' => 'start a workerman websocket server'),
        array('key' => ['wmt'], 'options' => '-p [PORT] -e [ENV]', 'desc' => 'start a workerman tcp server'),
        array('key' => ['wmu'], 'options' => '-p [PORT] -e [ENV]', 'desc' => 'start a workerman udp server'),
    ];
    private static $commandKeys = [];
    private static $help = '';
    private static $firstOpts = null;

    private static function c($msg)
    {
        echo self::HEAD . $msg;
    }

    public function __construct()
    {
        // command keys
        self::$commandKeys = [];
        // build help description
        self::$help .= "\n ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        self::$help .= " ┃ Command List:\n";
        self::$help .= " ┃\n";
        foreach (self::$commands as $c) {
            $key = implode(' | ', $c['key']);
            self::$commandKeys = array_merge(self::$commandKeys, $c['key']);
            self::$help .= " ┃     <{$key}> " . ($c['options'] ? "<options: {$c['options']}>" : '') . "\n";
            self::$help .= " ┃           {$c['desc']}\n";
        }
        self::$help .= " ┃\n ┃     (count:" . count(self::$commands) . ")\n";
        self::$help .= " ┃ Hope help you ~";
        self::$help .= "\n ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        //
        self::$firstOpts = getopt('o:c:e:p:');
    }

    public static function run($root_path)
    {
        if (empty($root_path)) {
            exit('not root path');
        }
        while (true) {
            $options = array();
            $opts = self::$firstOpts;
            if (!empty($opts['o'])) {
                self::$firstOpts = null;
                $command = $opts['o'];
                foreach ($opts as $ok => $ov) {
                    $ok !== 'o' && $options[$ok] = $ov;
                }
            } else {
                fwrite(STDOUT, "\n" . self::HEAD);
                $stdin = fgets(STDIN);
                $stdin = str_replace(["\r", "\n"], '', $stdin);
                if (empty($stdin)) {
                    self::c("type your command please!");
                    continue;
                }
                $stdin = explode(' ', $stdin);
                $command = array_shift($stdin);
                $stdinStr = trim(implode(' ', $stdin));
                $stdinStr = explode('-', $stdinStr);
                $filter = array_filter($stdinStr);
                foreach ($filter as $f) {
                    $f = explode(' ', $f);
                    $f1 = array_shift($f);
                    $f2 = trim(implode(' ', $f));
                    $options[$f1] = $f2;
                }
            }
            if (!in_array($command, self::$commandKeys)) {
                self::c(Color::lightRed("not command named: {$command},type \"help\" to get the command list"));
                continue;
            }
            try {
                switch ($command) {
                    case 'swh':
                        Core::get(SwooleHttp::class, $root_path, $options)->run();
                        break;
                    case 'swws':
                        Core::get(SwooleWebsocket::class, $root_path, $options)->run();
                        break;
                    case 'swt':
                        Core::get(SwooleTcp::class, $root_path, $options)->run();
                        break;
                    case 'swu':
                        Core::get(SwooleUdp::class, $root_path, $options)->run();
                        break;
                    case 'wmh':
                        Core::get(WorkermanHttp::class, $root_path, $options)->run();
                        break;
                    case 'wmws':
                        Core::get(WorkermanWebsocket::class, $root_path, $options)->run();
                        break;
                    case 'wmt':
                        Core::get(WorkermanTcp::class, $root_path, $options)->run();
                        break;
                    case 'wmu':
                        Core::get(WorkermanUdp::class, $root_path, $options)->run();
                        break;
                    case 'pkg':
                        Core::get(Package::class, $root_path, $options)->run();
                        break;
                    case 'cls':
                    case 'clear':
                        echo self::CLS;
                        break;
                    case 'ls':
                    case 'dir':
                        system('dir');
                        break;
                    case '-h':
                    case 'help':
                        self::c(Color::lightBlue(self::$help));
                        break;
                    case 'exit':
                    case 'die':
                        exit;
                    default:
                        self::c(Color::lightCyan("Not support command {$command} yet."));
                        break;
                }
            } catch (Exception $e) {
                self::c(Color::red($e->getMessage()));
            }
        }
    }
}