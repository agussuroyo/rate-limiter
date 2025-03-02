<?php

namespace AgusSuroyo\RateLimiter\Storage;

use AgusSuroyo\RateLimiter\Contracts\StorageInterface;
use Redis;

class RedisStorage implements StorageInterface
{
    private Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function getAttempts(string $key): array
    {
        return array_map('intval', $this->redis->lRange($key, 0, -1)) ?: [];
    }

    public function saveAttempts(string $key, array $attempts, int $decaySeconds): void
    {
        $this->redis->multi();
        $this->redis->del($key);
        foreach ($attempts as $attempt) {
            $this->redis->rPush($key, $attempt);
        }
        $this->redis->expire($key, $decaySeconds);
        $this->redis->exec();
    }

    public function clear(string $key): void
    {
        $this->redis->del($key);
    }
}