<?php

/**
 * 强制类型转换 -> string
 * @desc 兼容一般数据并运用递归处理数组内数据
 * @param $obj
 * @return array|string
 */
function parse_string($obj)
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = parse_string($v);
            } else {
                $obj[$k] = number_format($v, '', '', '');
            }
        }
    } else {
        $obj = number_format($obj, '', '', '');
    }
    return $obj;
}

/**
 * 强制类型转换 -> int
 * @desc 兼容一般数据并运用递归处理数组内数据
 * @param $obj
 * @param string $type
 * @return array|string
 */
function parse_int($obj, $type = 'round')
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = parse_int($v);
            } elseif (is_numeric($v) || !$v) {
                if ($type == 'round') $obj[$k] = round($v);
                elseif ($type == 'ceil') $obj[$k] = ceil($v);
                elseif ($type == 'floor') $obj[$k] = floor($v);
                elseif ($type == 'int') $obj[$k] = (int)$v;
            }
        }
    } elseif (is_numeric($obj) || !$obj) {
        if ($type == 'round') $obj = round($obj);
        elseif ($type == 'ceil') $obj = ceil($obj);
        elseif ($type == 'floor') $obj = floor($obj);
        elseif ($type == 'int') $obj = (int)$obj;
    }
    return $obj;
}

/**
 * 强制类型转换 -> real
 * @desc 兼容一般数据并运用递归处理数组内数据
 * @param $obj
 * @return array|string
 */
function parse_real($obj)
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = parse_real($v);
            } elseif (is_numeric($v) || !$v) {
                $obj[$k] = round($v, 0, 10);
            }
        }
    } elseif (is_numeric($obj) || !$obj) {
        $obj = round($obj, 0, 10);
    }
    return $obj;
}

/**
 * 科学计数法转回字符串
 * @param $obj
 * @return float|string
 */
function parse_scientificCountingMethod($obj)
{
    if (is_array($obj)) {
        foreach ($obj as $k => $v) {
            if (is_array($v)) {
                $obj[$k] = parse_scientificCountingMethod($v);
            } else {
                if (stripos($v, 'e+') === false) {
                    $obj[$k] = $v;
                } else {
                    $obj[$k] = (int)$v;
                }
            }
        }
    } else {
        if (stripos($obj, 'e+') !== false) {
            $obj = (int)$obj;
        }
    }
    return $obj;
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type = 0)
{
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

/**
 * 解析文件数据，获取标准格式的file
 * @param $fileData
 * @return array
 */
function parse_fileData($fileData)
{
    $newFileData = array();
    if (!$fileData) return $newFileData;
    foreach ($fileData as $fd) {
        if (!$fd || !isset($fd['name'])) continue;
        $isMulti = is_array($fd['name']);
        if (false === $isMulti) {
            $newFileData[] = array(
                'name' => $fd['name'],
                'type' => $fd['type'],
                'tmp_name' => $fd['tmp_name'],
                'error' => $fd['error'],
                'size' => $fd['size'],
            );
        } else {
            $qty = count($fd['name']);
            for ($i = 0; $i < $qty; $i += 1) {
                $newFileData[] = array(
                    'name' => $fd['name'][$i],
                    'type' => $fd['type'][$i],
                    'tmp_name' => $fd['tmp_name'][$i],
                    'error' => $fd['error'][$i],
                    'size' => $fd['size'][$i],
                );
            }
        }
    }
    return $newFileData;
}