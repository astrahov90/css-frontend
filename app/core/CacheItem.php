<?php

namespace core;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    private ?int $timeExpiresAfter;
    private string $key;
    private mixed $value;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt(?DateTimeInterface $expiration): static
    {
        $time = $expiration;
        if ($expiration instanceof DateTimeInterface)
            $time = $expiration->diff(new DateTime(), true);

        $this->timeExpiresAfter = $time;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter(DateInterval|int|null $time): static
    {
        if ($time instanceof DateInterval)
            $time = $time->days*86400 + $time->h*3600
                + $time->i*60 + $time->s;

        $this->timeExpiresAfter = $time;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimeExpiresAfter(): ?int
    {
        return $this->timeExpiresAfter;
    }
}