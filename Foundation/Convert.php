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
        public static function eol2br($str)
        {
            return nl2br($str);
        }

        /**
         * <br>切换行
         * @param $str
         * @return mixed
         */
        public static function br2nl($str)
        {
            return str_replace(["<br>", "<br/>"], PHP_EOL, $str);
        }

        /**
         * 将驼峰转为下划线命名
         * @param $str
         * @return string
         */
        public static function camel2underscore($str)
        {
            return strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $str));
        }

        /**
         * null to string
         * @param $obj
         * @return array|string
         */
        public static function null2String($obj)
        {
            if (is_array($obj)) {
                foreach ($obj as $k => $v) {
                    if (is_array($v)) {
                        $obj[$k] = static::null2String($v);
                    } elseif (is_null($v)) {
                        $obj[$k] = "";
                    }
                }
            } elseif (is_null($obj)) {
                $obj = "";
            }
            return $obj;
        }

        /**
         * 字符串转二进制01
         * @param $str
         * @return string
         */
        public static function str2bin($str)
        {
            if (!is_string($str)) return null;
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
        public static function bin2str($bin)
        {
            if (!is_string($bin)) return null;
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
        public static function limitConvert($data, $base_from)
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


    }

}