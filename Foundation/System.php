<?php

namespace {

    class System
    {

        /**
         * TODO 记录和统计时间（微秒）和内存使用情况
         * 使用方法:
         * <code>
         * G('begin'); // 记录开始标记位
         * // ... 区间运行代码
         * G('end'); // 记录结束标签位
         * echo G('begin','end',6); // 统计区间运行时间 精确到小数后6位
         * echo G('begin','end','m'); // 统计区间内存使用情况
         * 如果end标记位没有定义，则会自动以当前作为标记位
         * 其中统计内存使用需要 MEMORY_LIMIT_ON 常量为true才有效
         * </code>
         * @param string $start 开始标签
         * @param string $end 结束标签
         * @param integer|string $dec 小数位或者m
         * @return mixed
         */
        public static function rem($start, $end = '', $dec = 4)
        {
            static $_info = array();
            static $_mem = array();
            $memory_limit_on = function_exists('memory_get_usage');
            if (is_float($end)) { // 记录时间
                $_info[$start] = $end;
            } elseif (!empty($end)) { // 统计时间和内存使用
                if (!isset($_info[$end])) $_info[$end] = microtime(TRUE);
                if ($memory_limit_on && $dec == 'm') {
                    if (!isset($_mem[$end])) $_mem[$end] = memory_get_usage();
                    return number_format(($_mem[$end] - $_mem[$start]) / 1024);
                } else {
                    return number_format(($_info[$end] - $_info[$start]), $dec);
                }

            } else { // 记录时间和内存使用
                $_info[$start] = microtime(TRUE);
                if ($memory_limit_on) $_mem[$start] = memory_get_usage();
            }
            return null;
        }

        /**
         * 区分大小写的文件存在判断
         * @param string $filename 文件地址
         * @return boolean
         */
        public static function fileExistsCase($filename)
        {
            if (is_file($filename)) {
                if (IS_WINDOW) {
                    if (basename(realpath($filename)) != basename($filename))
                        return false;
                }
                return true;
            }
            return false;
        }

        /**
         * 优化的require_once
         * @param string $filename 文件地址
         * @return boolean
         */
        public static function requireCache($filename)
        {
            static $_importFiles = array();
            if (!isset($_importFiles[$filename])) {
                if (static::fileExistsCase($filename)) {
                    require $filename;
                    $_importFiles[$filename] = true;
                } else {
                    $_importFiles[$filename] = false;
                }
            }
            return $_importFiles[$filename];
        }

        /**
         * 载入目录
         * @param $dir
         * @param int $qty
         * @return int|void
         */
        public static function requireDir($dir, $qty = 0)
        {
            if (!is_dir($dir)) return;
            $files = opendir($dir);
            while ($file = readdir($files)) {
                if ($file != '.' && $file != '..') {
                    $realFile = $dir . '/' . $file;
                    if (is_dir($realFile)) {
                        $qty = static::requireDir($realFile, $qty);
                    } elseif (strpos($file, '.php') === false) {
                        continue;
                    } else {
                        static::requireCache($realFile);
                        $qty++;
                    }
                }
            }
            closedir($files);
            return $qty;
        }

        /**
         * 检查路径目录是否存在，存在则返回真实路径，不存在返回false
         * @param $path
         * @param bool $isBuild 是否自动创建不存在的目录
         * @return bool|string
         */
        public static function dirCheck($path, $isBuild = false)
        {
            $temp = str_replace('\\', '/', $path);
            if ($isBuild) {
                $p = explode('/', $temp);
                $tempLen = count($p);
                $temp = '';
                for ($i = 0; $i < $tempLen; $i++) {
                    $temp .= $p[$i] . DIRECTORY_SEPARATOR;
                    if (!is_dir($temp)) {
                        mkdir($temp);
                        @chmod($temp, 0777);
                    }
                }
            }
            $temp = realpath($temp) . DIRECTORY_SEPARATOR;
            return $temp ? $temp : false;
        }

        /**
         * 递归删除目录
         * @param $dir
         */
        public static function dirDel($dir)
        {
            if (!is_dir($dir)) {
                return;
            }
            $files = opendir($dir);
            while (false !== ($file = readdir($files))) {
                if ($file != '.' && $file != '..') {
                    $realDir = realpath($dir);
                    $realFile = $realDir . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($realFile)) {
                        static::dirDel($realFile);
                        @rmdir($realFile);
                    } else {
                        @unlink($realFile);
                    }
                }
            }
            closedir($files);
            @rmdir($dir);
        }

    }

}