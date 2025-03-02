<?php

namespace Tests\Feature;

use AgusSuroyo\RateLimiter\Storage\ArrayStorage;
use AgusSuroyo\RateLimiter\RateLimiter;
use PHPUnit\Framework\TestCase;

class RateLimiterArrayStorageTest extends TestCase
{
    public function testArrayStorage()
    {
        $storage = new ArrayStorage();
        $rateLimiter = new RateLimiter('testKey', 3, 60, $storage);

        $rateLimiter->hit();
        $rateLimiter->hit();
        $this->assertFalse($rateLimiter->tooManyAttempts());

        $rateLimiter->hit();
        $this->assertTrue($rateLimiter->tooManyAttempts());

        $rateLimiter->clear();
        $this->assertFalse($rateLimiter->tooManyAttempts());
    }

    public function testEdgeCaseDecayTimeExpiry()
    {
        $storage = new ArrayStorage();
        $rateLimiter = new RateLimiter('testKey', 2, 1, $storage);

        $rateLimiter->hit();
        sleep(2);
        $rateLimiter->hit();

        $this->assertFalse($rateLimiter->tooManyAttempts());
    }

    public function testEdgeCaseRapidHits()
    {
        $storage = new ArrayStorage();
        $rateLimiter = new RateLimiter('testKey', 5, 60, $storage);

        for ($i = 0; $i < 5; $i++) {
            $rateLimiter->hit();
        }

        $this->assertTrue($rateLimiter->tooManyAttempts());
    }
}
