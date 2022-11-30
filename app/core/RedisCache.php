<?php

namespace core;

use core\traits\CacheTrait;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Redis;

class RedisCache implements CacheItemPoolInterface
{
    use CacheTrait;

    private Redis $cacheObject;

    private bool $isConnected;

    public function __construct($cacheObject)
    {
        $this->cacheObject = $cacheObject;
    }

    /**
     * @throws \RedisException
     */
    public function connect(): void
    {
        $redisHost = Config::getRedisHost();
        $redisPort = Config::getRedisPort();

        try {
            $this->cacheObject->connect($redisHost, $redisPort);

            if (!empty($redisPassword))
            {
                $this->cacheObject->auth($redisPassword);
            }

            $this->isConnected = true;
        }
        catch (\RedisException)
        {
            $this->isConnected = false;
        }
    }

    /**
     * @throws \RedisException
     */
    public function clear(): bool
    {
        $this->cacheObject->flushDB();
    }

    public function commit(): bool
    {
        // TODO: Implement commit() method.
    }

    /**
     * @throws \RedisException
     */
    public function deleteItem(string $key): bool
    {
        return ($this->cacheObject->del($key) === 1);
    }

    /**
     * @throws \RedisException
     */
    public function deleteItems(array $keys): bool
    {
        $countDeleted = $this->cacheObject->del($keys);

        return ($countDeleted === count($keys));
    }

    public function getItem(string $key): CacheItemInterface
    {
        $value = $this->cacheObject->get($key);

        return new CacheItem($key, $value);

    }

    public function getItems(array $keys = []): iterable
    {
        // TODO: Implement getItems() method.
    }

    /**
     * @throws \RedisException
     */
    public function hasItem(string $key): bool
    {
        return $this->cacheObject->exists($key);
    }

    public function save(CacheItemInterface $item): bool
    {
        $this->cacheObject->set($item->getKey(), $item->get(), $item->getTimeExpiresAfter());
        return true;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        // TODO: Implement saveDeferred() method.
    }

    /**
     * @param string $pattern
     * @return iterable|bool
     * @throws \RedisException
     */
    public function scanItems(string $pattern='*'): array|bool
    {
        $iterator = null;
        return $this->cacheObject->scan($iterator,$pattern);
    }


}