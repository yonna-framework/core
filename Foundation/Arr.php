<?php

namespace {
    class Arr
    {

        /**
         * 获取值
         * @param $value
         * @return mixed
         */
        public static function value($value)
        {
            return $value instanceof Closure ? $value() : $value;
        }

        /**
         * Get an item from an array or object using "dot" notation.
         * 使用“点”符号从数组或对象中获取项。
         *
         * @param mixed $target
         * @param string|array $key
         * @param mixed $default
         * @return mixed
         */
        public static function data_get($target, $key, $default = null)
        {
            if (is_null($key)) {
                return $target;
            }
            $key = is_array($key) ? $key : explode('.', $key);
            while (!is_null($segment = array_shift($key))) {
                if ($segment === '*') {
                    if (!is_array($target)) {
                        return static::value($default);
                    }
                    $result = static::pluck($target, $key);
                    return in_array('*', $key) ? static::collapse($result) : $result;
                }
                if (static::accessible($target) && static::exists($target, $segment)) {
                    $target = $target[$segment];
                } elseif (is_object($target) && isset($target->{$segment})) {
                    $target = $target->{$segment};
                } else {
                    return static::value($default);
                }
            }
            return $target;
        }

        /**
         * 判断数组是否可以访问
         *
         * @param mixed $value
         * @return bool
         */
        public static function accessible($value)
        {
            return is_array($value) || $value instanceof ArrayAccess;
        }

        /**
         * 如果给定的键不存在于该数组，Arr::add 函数将给定的键值对加到数组中
         *
         * @param array $array
         * @param string $key
         * @param mixed $value
         * @return array
         */
        public static function add($array, $key, $value)
        {
            if (is_null(static::get($array, $key))) {
                static::set($array, $key, $value);
            }

            return $array;
        }

        /**
         * 将数组的每一个数组折成单一数组
         *
         * @param array $array
         * @return array
         */
        public static function collapse($array)
        {
            $results = [];
            foreach ($array as $values) {
                if (!is_array($values)) {
                    continue;
                }
                $results = array_merge($results, $values);
            }
            return $results;
        }

        /**
         * 交叉连接指定数组或集合的值，返回所有可能排列的笛卡尔积
         *
         * @param array ...$arrays
         * @return array
         */
        public static function crossJoin(...$arrays)
        {
            $results = [[]];

            foreach ($arrays as $index => $array) {
                $append = [];

                foreach ($results as $product) {
                    foreach ($array as $item) {
                        $product[$index] = $item;

                        $append[] = $product;
                    }
                }

                $results = $append;
            }

            return $results;
        }

        /**
         * 函数返回两个数组，一个包含原本数组的键，另一个包含原本数组的值
         *
         * @param array $array
         * @return array
         */
        public static function divide($array)
        {
            return [array_keys($array), array_values($array)];
        }

        /**
         * 把多维数组扁平化成一维数组，并用「点」式语法表示深度
         *
         * @param array $array
         * @param string $prepend
         * @return array
         */
        public static function dot($array, $prepend = '')
        {
            $results = [];

            foreach ($array as $key => $value) {
                if (is_array($value) && !empty($value)) {
                    $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
                } else {
                    $results[$prepend . $key] = $value;
                }
            }

            return $results;
        }

        /**
         * 从数组移除给定的键值对
         *
         * @param array $array
         * @param array|string $keys
         * @return array
         */
        public static function except($array, $keys)
        {
            static::forget($array, $keys);

            return $array;
        }

        /**
         * Determine if the given key exists in the provided array.
         *
         * @param ArrayAccess|array $array
         * @param string|int $key
         * @return bool
         */
        public static function exists($array, $key)
        {
            if ($array instanceof ArrayAccess) {
                return $array->offsetExists($key);
            }

            return array_key_exists($key, $array);
        }

        /**
         * 返回数组中第一个通过为真测试的元素
         *
         * @param array $array
         * @param callable|null $callback
         * @param mixed $default
         * @return mixed
         */
        public static function first($array, callable $callback = null, $default = null)
        {
            if (is_null($callback)) {
                if (empty($array)) {
                    return static::value($default);
                }

                foreach ($array as $item) {
                    return $item;
                }
            }
            foreach ($array as $key => $value) {
                if (call_user_func($callback, $value, $key)) {
                    return $value;
                }
            }
            return static::value($default);
        }

        /**
         * Return the last element in an array passing a given truth test.
         *
         * @param array $array
         * @param callable|null $callback
         * @param mixed $default
         * @return mixed
         */
        public static function last($array, callable $callback = null, $default = null)
        {
            if (is_null($callback)) {
                return empty($array) ? static::value($default) : end($array);
            }

            return static::first(array_reverse($array, true), $callback, $default);
        }

        /**
         * 将多维数组扁平化成一维
         *
         * @param array $array
         * @param int $depth
         * @return array
         */
        public static function flatten($array, $depth = INF)
        {
            $result = [];

            foreach ($array as $item) {
                if (!is_array($item)) {
                    $result[] = $item;
                } elseif ($depth === 1) {
                    $result = array_merge($result, array_values($item));
                } else {
                    $result = array_merge($result, static::flatten($item, $depth - 1));
                }
            }

            return $result;
        }

        /**
         * 以「点」式语法从深度嵌套数组移除给定的键值对
         *
         * @param array $array
         * @param array|string $keys
         * @return void
         */
        public static function forget(&$array, $keys)
        {
            $original = &$array;

            $keys = (array)$keys;

            if (count($keys) === 0) {
                return;
            }

            foreach ($keys as $key) {
                // if the exact key exists in the top-level, remove it
                if (static::exists($array, $key)) {
                    unset($array[$key]);

                    continue;
                }

                $parts = explode('.', $key);

                // clean up before each pass
                $array = &$original;

                while (count($parts) > 1) {
                    $part = array_shift($parts);

                    if (isset($array[$part]) && is_array($array[$part])) {
                        $array = &$array[$part];
                    } else {
                        continue 2;
                    }
                }

                unset($array[array_shift($parts)]);
            }
        }

        /**
         * 使用「点」式语法从深度嵌套数组取回给定的值
         *
         * @param ArrayAccess|array $array
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        public static function get($array, $key, $default = null)
        {
            if (!static::accessible($array)) {
                return static::value($default);
            }

            if (is_null($key)) {
                return $array;
            }

            if (static::exists($array, $key)) {
                return $array[$key];
            }

            if (strpos($key, '.') === false) {
                return $array[$key] ?? static::value($default);
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($array) && static::exists($array, $segment)) {
                    $array = $array[$segment];
                } else {
                    return static::value($default);
                }
            }

            return $array;
        }

        /**
         * 使用「点」式语法检查给定的项目是否存在于数组中
         *
         * @param ArrayAccess|array $array
         * @param string|array $keys
         * @return bool
         */
        public static function has($array, $keys)
        {
            if (is_null($keys)) {
                return false;
            }

            $keys = (array)$keys;

            if (!$array) {
                return false;
            }

            if ($keys === []) {
                return false;
            }

            foreach ($keys as $key) {
                $subKeyArray = $array;

                if (static::exists($array, $key)) {
                    continue;
                }

                foreach (explode('.', $key) as $segment) {
                    if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                        $subKeyArray = $subKeyArray[$segment];
                    } else {
                        return false;
                    }
                }
            }

            return true;
        }

        /**
         * 确定数组是否关联。
         * 如果数组没有以零开头的顺序数字键，则该数组是“关联”的。
         *
         * @param array $array
         * @return bool
         */
        public static function isAssoc(array $array)
        {
            $keys = array_keys($array);
            return array_keys($keys) !== $keys;
        }

        /**
         * 从数组返回给定的键值对
         *
         * @param array $array
         * @param array|string $keys
         * @return array
         */
        public static function only($array, $keys)
        {
            return array_intersect_key($array, array_flip((array)$keys));
        }

        /**
         * 从数组拉出一列给定的键值对
         *
         * @param array $array
         * @param string|array $value
         * @param string|array|null $key
         * @return array
         */
        public static function pluck($array, $value, $key = null)
        {
            $results = [];

            list($value, $key) = static::explodePluckParameters($value, $key);

            foreach ($array as $item) {
                $itemValue = static::data_get($item, $value);

                // If the key is "null", we will just append the value to the array and keep
                // looping. Otherwise we will key the array using the value of the key we
                // received from the developer. Then we'll return the final array form.
                if (is_null($key)) {
                    $results[] = $itemValue;
                } else {
                    $itemKey = static::data_get($item, $key);

                    if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                        $itemKey = (string)$itemKey;
                    }

                    $results[$itemKey] = $itemValue;
                }
            }

            return $results;
        }

        /**
         * 分解传递给“pluck”的“value”和“key”参数
         *
         * @param string|array $value
         * @param string|array|null $key
         * @return array
         */
        protected static function explodePluckParameters($value, $key)
        {
            $value = is_string($value) ? explode('.', $value) : $value;

            $key = is_null($key) || is_array($key) ? $key : explode('.', $key);

            return [$value, $key];
        }

        /**
         * 在数组前面增加一个项
         *
         * @param array $array
         * @param mixed $value
         * @param mixed $key
         * @return array
         */
        public static function prepend($array, $value, $key = null)
        {
            if (is_null($key)) {
                array_unshift($array, $value);
            } else {
                $array = [$key => $value] + $array;
            }

            return $array;
        }

        /**
         * 从数组移除并返回给定的键值对
         *
         * @param array $array
         * @param string $key
         * @param mixed $default
         * @return mixed
         */
        public static function pull(&$array, $key, $default = null)
        {
            $value = static::get($array, $key, $default);

            static::forget($array, $key);

            return $value;
        }

        /**
         * 从数组中获取一个或指定数量的随机值
         *
         * @param array $array
         * @param int|null $number
         * @return mixed
         *
         * @throws InvalidArgumentException
         */
        public static function random($array, $number = null)
        {
            $requested = is_null($number) ? 1 : $number;

            $count = count($array);

            if ($requested > $count) {
                throw new InvalidArgumentException(
                    "You requested {$requested} items, but there are only {$count} items available."
                );
            }

            if (is_null($number)) {
                return $array[array_rand($array)];
            }

            if ((int)$number === 0) {
                return [];
            }

            $keys = array_rand($array, $number);

            $results = [];

            foreach ((array)$keys as $key) {
                $results[] = $array[$key];
            }

            return $results;
        }

        /**
         * 使用「点」式语法在深度嵌套数组中写入值
         *
         * @param array $array
         * @param string $key
         * @param mixed $value
         * @return array
         */
        public static function set(&$array, $key, $value)
        {
            if (is_null($key)) {
                return $array = $value;
            }

            $keys = explode('.', $key);

            while (count($keys) > 1) {
                $key = array_shift($keys);

                // If the key doesn't exist at this depth, we will just create an empty array
                // to hold the next value, allowing us to create the arrays to hold final
                // values at the correct depth. Then we'll keep digging into the array.
                //如果该键在此深度不存在，我们将只创建一个空数组
                //保存下一个值，允许我们创建数组以保存final
                //正确深度的值。然后我们继续挖掘阵列
                if (!isset($array[$key]) || !is_array($array[$key])) {
                    $array[$key] = [];
                }

                $array = &$array[$key];
            }

            $array[array_shift($keys)] = $value;

            return $array;
        }

        /**
         * 洗牌
         *
         * @param array $array
         * @return array
         */
        public static function shuffle($array)
        {
            shuffle($array);

            return $array;
        }

        /**
         * 不影响原数组排序
         *
         * @param array $array
         * @return array
         */
        public static function sort($array)
        {
            $arr = $array;
            sort($arr);
            return $arr;
        }

        /**
         * 使用 sort 函数递归排序数组
         *
         * @param array $array
         * @return array
         */
        public static function sortRecursive($array)
        {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $value = static::sortRecursive($value);
                }
            }

            if (static::isAssoc($array)) {
                ksort($array);
            } else {
                sort($array);
            }

            return $array;
        }

        /**
         * 使用给定的闭包过滤数组
         *
         * @param array $array
         * @param callable $callback
         * @return array
         */
        public static function where($array, callable $callback)
        {
            return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
        }

        /**
         * 数组包裹
         * 如果不是数组，就变成数组，如果是空的，返回[],否则返回原数据
         *
         * @param mixed $value
         * @return array
         */
        public static function wrap($value)
        {
            return !is_array($value) ? [$value] : $value;
        }
    }
}


