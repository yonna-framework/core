<?php

namespace Yonna\Console;

class Color
{
    private const BLACK = "30";
    private const RED = "31";
    private const GREEN = "32";
    private const YELLOW = "33";
    private const BLUE = "34";
    private const MAGENTA = "35";
    private const CYAN = "36";
    private const LIGHT_GRAY = "37";
    private const DARK_GRAY = "90";
    private const LIGHT_RED = "91";
    private const LIGHT_GREEN = "92";
    private const LIGHT_YELLOW = "93";
    private const LIGHT_BLUE = "94";
    private const LIGHT_MAGENTA = "95";
    private const LIGHT_CYAN = "96";
    private const WHITE = "97";
    // style
    const BOLD = "1";
    const DIM = "2";
    const UNDERLINED = "4";
    const REVERSE = "7";
    const HIDDEN = "8";

    private static function wrap($msg, $color, $style = array())
    {
        $str = "\033[";
        if (!empty($style)) {
            array_unshift($style, $color);
        } else {
            $style = array($color);
        }
        $str .= implode(';', $style);
        $str .= "m";
        $str .= $msg . "\033[0m";
        return $str;
    }

    public static function black($msg, $style = array())
    {
        return self::wrap($msg, self::BLACK, $style);
    }

    public static function red($msg, $style = array())
    {
        return self::wrap($msg, self::RED, $style);
    }

    public static function green($msg, $style = array())
    {
        return self::wrap($msg, self::GREEN, $style);
    }

    public static function yellow($msg, $style = array())
    {
        return self::wrap($msg, self::YELLOW, $style);
    }

    public static function blue($msg, $style = array())
    {
        return self::wrap($msg, self::BLUE, $style);
    }

    public static function magenta($msg, $style = array())
    {
        return self::wrap($msg, self::MAGENTA, $style);
    }

    public static function cyan($msg, $style = array())
    {
        return self::wrap($msg, self::CYAN, $style);
    }

    public static function lightGray($msg, $style = array())
    {
        return self::wrap($msg, self::LIGHT_GRAY, $style);
    }

    public static function darkGray($msg, $style = array())
    {
        return self::wrap($msg, self::DARK_GRAY, $style);
    }

    public static function lightRed($msg, $style = array())
    {
        return self::wrap($msg, self::LIGHT_RED, $style);
    }

    public static function lightGreen($msg, $style = array())
    {
        return self::wrap($msg, self::LIGHT_GREEN, $style);
    }

    public static function lightYellow($msg, $style = array())
    {
        return self::wrap($msg, self::LIGHT_YELLOW, $style);
    }

    public static function lightBlue($msg, $style = array())
    {
        return self::wrap($msg, self::LIGHT_BLUE, $style);
    }

    public static function lightMagenta($msg, $style = array())
    {
        return self::wrap($msg, self::LIGHT_MAGENTA, $style);
    }

    public static function lightCyan($msg, $style = array())
    {
        return self::wrap($msg, self::LIGHT_CYAN, $style);
    }

    public static function white($msg, $style = array())
    {
        return self::wrap($msg, self::WHITE, $style);
    }
}