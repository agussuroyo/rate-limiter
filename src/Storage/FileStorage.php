<?php

namespace AgusSuroyo\RateLimiter\Storage;

use AgusSuroyo\RateLimiter\Contracts\StorageInterface;

class FileStorage implements StorageInterface
{
    private string $storagePath;

    public function __construct(string $storagePath = __DIR__ . '/storages/rate-limiter')
    {
        $this->storagePath = rtrim($storagePath, '/') . '/';
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    private function getFilePath(string $key): string
    {
        return $this->storagePath . md5($key) . '.json';
    }

    public function getAttempts(string $key): array
    {
        $file = $this->getFilePath($key);
        if (!file_exists($file)) {
            return [];
        }
        return json_decode(file_get_contents($file), true) ?? [];
    }

    public function saveAttempts(string $key, array $attempts, int $decaySeconds): void
    {
        file_put_contents($this->getFilePath($key), json_encode($attempts), LOCK_EX);
    }

    public function clear(string $key): void
    {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }
}