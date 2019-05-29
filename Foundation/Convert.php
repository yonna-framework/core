<?php

namespace {

    class Convert
    {

        /**
         * @param $bn
         * @param $sn
         * @return int
         */
        public static function kmod($bn, $sn)
        {
            return intval(fmod(floatval($bn), $sn));
        }

        /**
         * 换行切<br>
         * @param $str
         * @return mixed
         */
        public static function eol2br(string $str)
        {
            return nl2br($str);
        }

        /**
         * <br>切换行
         * @param $str
         * @return mixed
         */
        public static function br2nl(string $str)
        {
            return str_replace(["<br>", "<br/>"], PHP_EOL, $str);
        }

        /**
         * null to string
         * @param $data
         * @return array|string
         */
        public static function null2String($data)
        {
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    if (is_array($v)) {
                        $data[$k] = static::null2String($v);
                    } elseif (is_null($v)) {
                        $data[$k] = '';
                    }
                }
            } elseif (is_null($data)) {
                $data = '';
            }
            return $data;
        }

        /**
         * null to string
         * @param $data
         * @return array|string
         */
        public static function obj2String($data)
        {
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    if (is_array($v)) {
                        $data[$k] = static::obj2String($v);
                    } elseif (is_null($v)) {
                        $data[$k] = '';
                    } elseif (is_object($v)) {
                        $data[$k] = 'object#' . get_class($v);
                    }
                }
            } elseif (is_null($data)) {
                $data = '';
            } elseif (is_object($data)) {
                $data = 'object#' . get_class($data);
            }
            return $data;
        }

        /**
         * 字符串转二进制01
         * @param $str
         * @return string
         */
        public static function str2bin(string $str): string
        {
            if (!is_string($str)) return '';
            $value = unpack('H*', $str);
            $value = str_split($value[1], 1);
            $bin = '';
            foreach ($value as $v) {
                $b = str_pad(base_convert($v, 16, 2), 4, '0', STR_PAD_LEFT);
                $bin .= $b;
            }
            return $bin;
        }

        /**
         * 二进制01字符串转
         * @param $bin
         * @return string
         */
        public static function bin2str(string $bin): string
        {
            if (!is_string($bin)) return '';
            $bin = str_split($bin, 4);
            $str = '';
            foreach ($bin as $v) {
                $str .= base_convert($v, 2, 16);
            }
            $str = pack('H*', $str);
            return $str;
        }

        /**
         * 任意进制转“允许极限{$chars_map.length}进制"
         * @param $data
         * @param $base_from
         * @return string
         */
        public static function limitConvert($data, int $base_from)
        {
            $chars_map = [
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
                '_', '~', '!', '@', '$', '[', ']', '-', '·',
            ];
            $dividend = count($chars_map);
            if ($base_from >= $dividend) {
                return null;
            }
            if ($base_from !== 10) {
                $data = base_convert($data, $base_from, 10);
            }
            $base64_chars = [];
            while ($data > $dividend) {
                $r = static::kmod($data, $dividend);
                $data = ($data - $r) / $dividend;
                $base64_chars[] = $chars_map[$r];
            }
            $r = static::kmod($data, $dividend);
            $base64_chars[] = $chars_map[$r];
            return join('', array_reverse($base64_chars));
        }

        /**
         * 数组转html
         * @param array $arr
         * @param string $prevKey
         * @return string
         */
        public static function arr2html(array $arr, string $prevKey = '-root'): string
        {
            $str = "<table class='phpure{$prevKey}'>";
            foreach ($arr as $k => $v) {
                $str .= "<tr>";
                $str .= "<td class='key'>{$k}</td>";
                $str .= "<td class='value'>";
                if (is_array($v)) {
                    $str .= self::arr2html($v, "{$prevKey}-{$k}");
                } else {
                    $str .= (string)$v;
                }
                $str .= "</td>";
                $str .= "</tr>";
            }
            $str .= "</table>";
            return $str;
        }


    }

}