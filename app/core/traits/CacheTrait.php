<?php

namespace core\traits;

use \Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

trait CacheTrait
{
    private static CacheItemPoolInterface $cachePool;

    /**
     * @param CacheItemPoolInterface $cachePool
     * @return void
     */
    public static function setInstance(CacheItemPoolInterface $cachePool):void
    {
        static::$cachePool = $cachePool;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public static function getInstance():CacheItemPoolInterface
    {
        return static::$cachePool;
    }

    /**
     * @param $redisKey
     * @param callable $modelRequest
     * @param int|null $expiresAfter
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function getCacheOrDoRequest($redisKey, callable $modelRequest, ?int $expiresAfter=null):mixed
    {
        $redisCache = static::getInstance();

        if ($redisCache->isConnected)
        {
            $cacheItem = $redisCache->getItem($redisKey);
            $result = json_decode($cacheItem->get());
            if (!$result)
            {
                $result = $modelRequest();

                $cacheItem->set(json_encode($result));
                if ($expiresAfter)
                    $cacheItem->expiresAfter($expiresAfter);
                $redisCache->save($cacheItem);
            }
        }
        else
            $result = $modelRequest();

        return $result;
    }

    public static function clearCache(?string $pattern='*'):void
    {
        $redisCache = static::getInstance();

        if ($redisCache->isConnected)
        {
            $keysFounded = $redisCache->scanItems($pattern);
            if ($keysFounded)
                $redisCache->deleteItems($keysFounded);
        }
    }

}