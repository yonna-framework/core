#!/usr/bin/env php
<?php

namespace PhpureCore;

/**
 * pure 的命令窗体
 * Class Commander
 */
class Exec
{

    const HEAD = '>hPHP: ';
    const CLS = "\e[H\e[J";

    private static $commands = [
        array('key' => ['cls', 'clear'], 'options' => '', 'desc' => 'clean screen'),
        array('key' => ['ls', 'dir'], 'options' => '', 'desc' => 'explore dir'),
        array('key' => ['die', 'exit'], 'options' => '', 'desc' => 'exit hPHP'),
        array('key' => ['-h', 'help'], 'options' => '', 'desc' => 'get command list'),
        array('key' => ['swh'], 'options' => '-p [PORT]', 'desc' => 'start a swoole http server'),
        array('key' => ['swws'], 'options' => '-p [PORT]', 'desc' => 'start a swoole websocket server'),
        array('key' => ['swt'], 'options' => '-p [PORT]', 'desc' => 'start a swoole tcp server'),
        array('key' => ['pkg'], 'options' => '-c [CONFIG PATH]', 'desc' => 'package project'),
    ];
    private static $commandKeys = [];
    private static $help = '';

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
    }

    public static function run()
    {
        while (true) {
            fwrite(STDOUT, "\n" . self::HEAD);
            $stdin = fgets(STDIN);
            $stdin = str_replace(["\r", "\n"], '', $stdin);
            if (empty($stdin)) {
                self::c("type your command please!");
                continue;
            }
            $stdin = explode(' ', $stdin);
            $command = array_shift($stdin);
            $options = trim(implode(' ', $stdin));
            if (!in_array($command, self::$commandKeys)) {
                self::c("not command named: {$command},type \"help\" to get the command list");
                continue;
            }
            switch ($command) {
                case 'swh':
                    if (!$options) {
                        self::c('not port');
                        break;
                    }
                    system("php hSwoole.http.php {$options}");
                    break;
                case 'swws':
                    if (!$options) {
                        self::c('not port');
                        break;
                    }
                    system("php hSwoole.websocket.php {$options}");
                    break;
                case 'swt':
                    if (!$options) {
                        self::c('not port');
                        break;
                    }
                    system("php hSwoole.tcp.php {$options}");
                    break;
                case 'pkg':
                    if (!$options) {
                        self::c('not config path');
                        break;
                    }
                    system("php hPackage.php {$options}");
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
                    self::c(self::$help);
                    break;
                case 'exit':
                case 'die':
                    exit;
                default:
                    self::c("command is {$command}");
                    break;
            }
        }
    }
}