<?php

namespace AgusSuroyo\RateLimiter\Contracts;

interface StorageInterface
{
    public function getAttempts(string $key): array;
    public function saveAttempts(string $key, array $attempts, int $decaySeconds): void;
    public function clear(string $key): void;
}
