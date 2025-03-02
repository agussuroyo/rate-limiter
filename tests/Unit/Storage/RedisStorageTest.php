<?php

namespace Tests\Unit\Storage;

use PHPUnit\Framework\TestCase;
use AgusSuroyo\RateLimiter\Storage\RedisStorage;
use Redis;

class RedisStorageTest extends TestCase
{
    private Redis $redis;
    private RedisStorage $storage;
    private string $key;

    protected function setUp(): void
    {
        $this->redis = new Redis();
        $this->redis->connect(
            getenv('REDIS_HOST') ?: '127.0.0.1',
            getenv('REDIS_PORT') ?: 6379
        );
        $this->storage = new RedisStorage($this->redis);
        $this->key = 'test_key';
    }

    public function testSaveAndGetAttempts()
    {
        $attempts = [time(), time() - 10, time() - 20];
        $decaySeconds = 60;

        $this->storage->saveAttempts($this->key, $attempts, $decaySeconds);
        $retrievedAttempts = $this->storage->getAttempts($this->key);

        $this->assertEquals($attempts, $retrievedAttempts);
    }

    public function testClearRemovesAttempts()
    {
        $this->storage->saveAttempts($this->key, [time()], 60);
        $this->storage->clear($this->key);

        $this->assertEmpty($this->storage->getAttempts($this->key));
    }

    protected function tearDown(): void
    {
        $this->redis->del($this->key);
    }
}
