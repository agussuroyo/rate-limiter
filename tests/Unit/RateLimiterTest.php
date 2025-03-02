<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use AgusSuroyo\RateLimiter\RateLimiter;
use AgusSuroyo\RateLimiter\Contracts\StorageInterface;
use Mockery;

class RateLimiterTest extends TestCase
{
    public function testHitIncrementsAttempts()
    {
        $storage = Mockery::mock(StorageInterface::class);
        $storage->shouldReceive('getAttempts')->with('test_key')->andReturn([]);
        $storage->shouldReceive('saveAttempts')->once();

        $rateLimiter = new RateLimiter('test_key', 5, 60, $storage);
        $rateLimiter->hit();

        $this->assertTrue(true); // Dummy assertion to ensure no errors
    }

    public function testTooManyAttemptsReturnsTrueWhenLimitExceeded()
    {
        $storage = Mockery::mock(StorageInterface::class);
        $storage->shouldReceive('getAttempts')->with('test_key')->andReturn([time(), time() - 10, time() - 20]);
        $storage->shouldReceive('saveAttempts');

        $rateLimiter = new RateLimiter('test_key', 2, 60, $storage);
        $this->assertTrue($rateLimiter->tooManyAttempts());
    }

    public function testTooManyAttemptsReturnsFalseWhenLimitNotExceeded()
    {
        $storage = Mockery::mock(StorageInterface::class);
        $storage->shouldReceive('getAttempts')->with('test_key2')->andReturn([time() - 50]);
        $storage->shouldReceive('saveAttempts');

        $rateLimiter = new RateLimiter('test_key2', 2, 60, $storage);
        $this->assertFalse($rateLimiter->tooManyAttempts());
    }

    public function testClearRemovesAttempts()
    {
        $storage = Mockery::mock(StorageInterface::class);
        $storage->shouldReceive('clear')->with('test_key')->once();

        $rateLimiter = new RateLimiter('test_key', 5, 60, $storage);
        $rateLimiter->clear();

        $this->assertTrue(true); // Dummy assertion to ensure no errors
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
