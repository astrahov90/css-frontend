<?php

namespace core\traits;

use \Psr\Cache\CacheItemPoolInterface;

trait CacheTrait
{
    private static CacheItemPoolInterface $cachePool;

    public static function setInstance(CacheItemPoolInterface $cachePool):void
    {
        static::$cachePool = $cachePool;
    }

    public static function getInstance():CacheItemPoolInterface
    {
        return static::$cachePool;
    }

}