<?php

namespace {

    class Filter
    {

        /**
         * 过滤掉emoji表情
         * @param $str
         * @return null|string|string[]
         */
        public static function emoji($str)
        {
            if ($str) {
                $str = preg_replace_callback('/./u', function (array $match) {
                    return strlen($match[0]) >= 4 ? '' : $match[0];
                }, $str);
                return $str;
            } else {
                return '';
            }
        }

    }
}