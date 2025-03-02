# RateLimiter

RateLimiter is a simple and flexible PHP package for rate limiting requests using different storage backends such as file storage and Redis.

## Features
- Set request limits dynamically.
- Supports multiple storage backends (File, Redis).
- Easy-to-use API.
- Complies with SOLID principles for extensibility.

## Installation
You can install the package via Composer:

```bash
composer require agussuroyo/rate-limiter
```

## Usage

### Basic Usage
```php
use AgusSuroyo\RateLimiter\RateLimiter;

$limiter = new RateLimiter('guest', 5, 60); // Key, Max Attempts, Decay Seconds

if ($limiter->tooManyAttempts()) {
    echo "Too many requests. Please try again later.";
    return;
}

$limiter->hit();
echo "Request allowed.";
```

### Constructor Parameters
The `RateLimiter` constructor accepts four parameters:

1. **Key (string)** - A unique identifier for the rate limit (e.g., user ID or IP address).
2. **Max Attempts (int)** - The maximum number of allowed requests within the decay period.
3. **Decay Seconds (int)** - The time window (in seconds) before the attempt count resets.
4. **Storage (StorageInterface, optional)** - The storage mechanism to persist request attempts. If not provided, it defaults to `FileStorage`.

### Using Redis Storage
```php
use AgusSuroyo\RateLimiter\RateLimiter;
use AgusSuroyo\RateLimiter\Storage\RedisStorage;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$storage = new RedisStorage($redis);

$limiter = new RateLimiter('guest', 5, 60, $storage);

if ($limiter->tooManyAttempts()) {
    echo "Too many requests. Please try again later.";
    return;
}

$limiter->hit();
echo "Request allowed.";
```

### Clearing Rate Limit
```php
$limiter->clear();
```

### Creating a Custom Storage
To implement a custom storage, create a class that implements `StorageInterface`:

```php
use AgusSuroyo\RateLimiter\Contracts\StorageInterface;

class CustomStorage implements StorageInterface
{
    private array $data = [];

    public function getAttempts(string $key): array
    {
        return $this->data[$key] ?? [];
    }

    public function saveAttempts(string $key, array $attempts, int $decaySeconds): void
    {
        $this->data[$key] = $attempts;
    }

    public function clear(string $key): void
    {
        unset($this->data[$key]);
    }
}

$customStorage = new CustomStorage();
$limiter = new RateLimiter('guest', 5, 60, $customStorage);
```

## Testing
Run unit tests using PHPUnit:
```bash
vendor/bin/phpunit
```

## Contributing
Contributions are welcome! Please submit a pull request or open an issue.

## License
This package is open-source software licensed under the [MIT license](LICENSE).

