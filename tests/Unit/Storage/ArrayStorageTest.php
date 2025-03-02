<?php

namespace Tests\Unit\Storage;

use AgusSuroyo\RateLimiter\Storage\ArrayStorage;
use PHPUnit\Framework\TestCase;

class ArrayStorageTest extends TestCase
{
    public function testGetAttempts()
    {
        $storage = new ArrayStorage();
        $this->assertSame([], $storage->getAttempts('test_key'));
    }

    public function testSaveAttempts()
    {
        $storage = new ArrayStorage();
        $storage->saveAttempts('test_key', [time()], 60);
        $this->assertSame([time()], $storage->getAttempts('test_key'));
    }

    public function testClear()
    {
        $storage = new ArrayStorage();
        $storage->saveAttempts('test_key', [time()], 60);
        $storage->clear('test_key');
        $this->assertSame([], $storage->getAttempts('test_key'));
    }
}