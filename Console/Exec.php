<?php

namespace PhpureCore\Console;

use Exception;
use PhpureCore\Core;

/**
 * pure 的命令窗体
 * Class Commander
 */
class Exec
{

    const HEAD = '>Pure<: ';
    const CLS = "\e[H\e[J";

    private static $commands = [
        array('key' => ['cls', 'clear'], 'options' => '', 'desc' => 'clean screen'),
        array('key' => ['ls', 'dir'], 'options' => '', 'desc' => 'explore dir'),
        array('key' => ['die', 'exit'], 'options' => '', 'desc' => 'exit exec'),
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

    public static function run($root_path)
    {
        if (empty($root_path)) {
            exit('not root path');
        }
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
            $stdinStr = trim(implode(' ', $stdin));
            $stdinStr = explode('-', $stdinStr);
            $filter = array_filter($stdinStr);
            $options = array();
            foreach ($filter as $f) {
                $f = explode(' ', $f);
                $f1 = array_shift($f);
                $f2 = trim(implode(' ', $f));
                $options[$f1] = $f2;
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
                        Core::get(SwooleWebsocket::class)->run($root_path, $options);
                        break;
                    case 'swt':
                        if (!$options) {
                            Core::get(SwooleTcp::class)->run($root_path, $options);
                            break;
                        }
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