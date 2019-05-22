<?php

/**
 * 生成N位随机验证码(大小写+数字)
 * @param int $len
 * @return string
 */
function rand_char($len = 6)
{
    $codeLib = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    ];
    $codeMax = count($codeLib);
    $code = '';
    for ($i = 0; $i < $len; $i++) {
        $code .= $codeLib[rand(0, $codeMax - 1)];
    }
    return $code;
}

/**
 * 生成N位随机数字
 * @param int $len
 * @return string
 */
function rand_charNum($len = 6)
{
    $codeLib = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $codeMax = count($codeLib);
    $code = '';
    for ($i = 0; $i < $len; $i++) {
        $code .= $codeLib[rand(0, $codeMax - 1)];
    }
    return $code;
}

/**
 * 生成N位随机数字
 * @param int $len
 * @param bool $isUpper
 * @return string
 */
function rand_charLetter($len = 6, $isUpper = false)
{
    $codeLib = $isUpper
        ? ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
        : ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    $codeMax = count($codeLib);
    $code = '';
    for ($i = 0; $i < $len; $i++) {
        $code .= $codeLib[rand(0, $codeMax - 1)];
    }
    return $code;
}