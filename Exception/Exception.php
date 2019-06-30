<?php

namespace Yonna\Exception;

class Exception
{

    private const THROW = 'THROW';
    private const ABORT = 'ABORT';

    private static function e($type, $msg)
    {
        throw new \Exception("[{$type}]{$msg}");
    }

    public static function throw($msg)
    {
        self::e(self::THROW, $msg);
    }

    public static function abort($msg)
    {
        self::e(self::ABORT, $msg);
    }

}