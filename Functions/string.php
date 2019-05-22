<?php

/**
 * 只替换第一个
 * @param $needle
 * @param $replace
 * @param $haystack
 * @return mixed
 */
function str_replace_once($needle, $replace, $haystack)
{
    $pos = strpos($haystack, $needle);
    return $pos === false ? $haystack : substr_replace($haystack, $replace, $pos, strlen($needle));
}

/**
 * 只替换第一个 适配编码
 * @param $needle
 * @param $replace
 * @param $haystack
 * @param string $encoding
 * @return mixed
 */
function mb_str_replace_once($needle, $replace, $haystack, $encoding = 'utf8')
{
    $pos = mb_strpos($haystack, $needle, 0, $encoding);
    return $pos === false ? $haystack : substr_replace($haystack, $replace, $pos, mb_strlen($needle, $encoding));
}

/**
 * 字符串反转
 * @param $str
 * @return string
 */
function str_reserve($str)
{
    if (!$str) return (string)$str;
    return implode(array_reverse(str_split($str)));
}