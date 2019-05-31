<?php
/**
 * 自动缓存机制
 * 除下面常用类型外可自由设置数字字符串如：'90' 代表90秒
 * 特别注意，当设置的时间少于 10 秒时，会重设为 10 秒
 * you can always set a number
 */

namespace PhpureCore\Mapping;

class AutoCache extends Mapping
{

    const TRUE = 'true';
    const FALSE = 'false';
    const FOREVER = 'forever';
    const ONE_MINUTE = '60';
    const FIVE_MINUTE = '300';
    const TEN_MINUTE = '600';
    const HALF_ONE_HOUR = '1800';
    const ONE_HOUR = '3600';
    const ONE_DAY = '86400';
    const ONE_WEEK = '604800';

}