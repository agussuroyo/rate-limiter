<?php

namespace AgusSuroyo\RateLimiter\Storage;

use AgusSuroyo\RateLimiter\Contracts\StorageInterface;

class ArrayStorage implements StorageInterface
{
    private array $storage = [];

    public function getAttempts(string $key): array
    {
        return $this->storage[$key] ?? [];
    }

    public function saveAttempts(string $key, array $attempts, int $decaySeconds): void
    {
        $this->storage[$key] = $attempts;
    }

    public function clear(string $key): void
    {
        unset($this->storage[$key]);
    }
}