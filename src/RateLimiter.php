<?php

namespace AgusSuroyo\RateLimiter;

use AgusSuroyo\RateLimiter\Contracts\StorageInterface;
use AgusSuroyo\RateLimiter\Storage\FileStorage;

class RateLimiter
{
    private string $key;
    private int $maxAttempts;
    private int $decaySeconds;
    private StorageInterface $storage;

    public function __construct(string $key = 'guest', int $maxAttempts = 10, int $decaySeconds = 60, StorageInterface $storage = null)
    {
        $this->setKey($key);
        $this->setMaxAttempts($maxAttempts);
        $this->setDecaySeconds($decaySeconds);
        $this->setStorage($storage ?? new FileStorage());
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function setMaxAttempts(int $maxAttempts): void
    {
        $this->maxAttempts = $maxAttempts;
    }

    public function setDecaySeconds(int $decaySeconds): void
    {
        $this->decaySeconds = $decaySeconds;
    }

    public function setStorage(StorageInterface $storage): void
    {
        $this->storage = $storage;
    }

    public function hit(): void
    {
        $attempts = $this->storage->getAttempts($this->key);
        $attempts[] = time();
        $this->storage->saveAttempts($this->key, $attempts, $this->decaySeconds);
    }

    public function tooManyAttempts(): bool
    {
        $now = time();
        $attempts = array_filter($this->storage->getAttempts($this->key), fn($timestamp) => $timestamp >= ($now - $this->decaySeconds));

        if (count($attempts) >= $this->maxAttempts) {
            return true;
        }
        
        $this->storage->saveAttempts($this->key, $attempts, $this->decaySeconds);
        return false;
    }

    public function clear(): void
    {
        $this->storage->clear($this->key);
    }
}