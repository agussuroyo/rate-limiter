<?php

namespace Tests\Unit\Storage;

use PHPUnit\Framework\TestCase;
use AgusSuroyo\RateLimiter\Storage\FileStorage;

class FileStorageTest extends TestCase
{
    private string $storagePath;
    private FileStorage $storage;

    protected function setUp(): void
    {
        $this->storagePath = sys_get_temp_dir() . '/storages/rate-limiter';
        $this->storage = new FileStorage($this->storagePath);
    }

    public function testSaveAndGetAttempts()
    {
        $key = 'test_key';
        $attempts = [time(), time() - 10, time() - 20];
        $decaySeconds = 60;

        $this->storage->saveAttempts($key, $attempts, $decaySeconds);
        $retrievedAttempts = $this->storage->getAttempts($key);

        $this->assertEquals($attempts, $retrievedAttempts);
    }

    public function testClearRemovesAttempts()
    {
        $key = 'test_key';
        $this->storage->saveAttempts($key, [time()], 60);
        $this->storage->clear($key);
        
        $this->assertEmpty($this->storage->getAttempts($key));
    }

    protected function tearDown(): void
    {
        // clear path
        if (is_dir($this->storagePath)) {
            $files = glob($this->storagePath . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($this->storagePath);
        }
    }
}
