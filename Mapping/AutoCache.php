<?php
/**
 * 自动缓存机制
 * 除下面常用类型外可自由设置数字字符串如：'90' 代表90秒
 * 特别注意，当设置的时间大于0(不限时)并少于最小缓存时间(def.10s)，会重设为最小缓存时间
 * you can always set a number
 * @see \PhpureCore\Config\Database
 * @see \PhpureCore\Database\Cache
 */

namespace PhpureCore\Mapping;

class AutoCache extends Mapping
{

    const FOREVER = 'forever';
    const ONE_MINUTE = '60';
    const FIVE_MINUTE = '300';
    const TEN_MINUTE = '600';
    const HALF_ONE_HOUR = '1800';
    const ONE_HOUR = '3600';
    const ONE_DAY = '86400';
    const ONE_WEEK = '604800';

}