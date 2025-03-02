<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use AgusSuroyo\RateLimiter\RateLimiter;
use AgusSuroyo\RateLimiter\Storage\FileStorage;

class RateLimiterFileStorageTest extends TestCase
{
    private RateLimiter $rateLimiter;
    private string $storagePath;
    private string $key;

    protected function setUp(): void
    {
        $this->storagePath = sys_get_temp_dir() . '/storages/rate-limiter';
        $storage = new FileStorage($this->storagePath);
        $this->key = 'feature_test_key';
        $this->rateLimiter = new RateLimiter($this->key, 3, 60, $storage);
    }

    public function testRateLimitAllowsWithinLimit()
    {
        $this->rateLimiter->hit();
        $this->rateLimiter->hit();
        $this->assertFalse($this->rateLimiter->tooManyAttempts());
    }

    public function testRateLimitBlocksAfterExceedingLimit()
    {
        $this->rateLimiter->hit();
        $this->rateLimiter->hit();
        $this->rateLimiter->hit();
        $this->assertTrue($this->rateLimiter->tooManyAttempts());
    }

    public function testRateLimitResetsAfterClear()
    {
        $this->rateLimiter->hit();
        $this->rateLimiter->hit();
        $this->rateLimiter->clear();
        $this->assertFalse($this->rateLimiter->tooManyAttempts());
    }

    protected function tearDown(): void
    {
        if (is_dir($this->storagePath)) {
            $files = glob($this->storagePath . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($this->storagePath);
        }
    }
}
