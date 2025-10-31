<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Cache\Lock;
use App\Services\Logging\LoggingService;
use Exception;
use Ramsey\Uuid\Uuid;

class CacheService
{
    protected LoggingService $loggingService;
    protected array $defaultOptions = [
        'ttl' => 3600,
        'compress' => true,
        'serialize' => true,
        'tags' => [],
        'lock_timeout' => 10,
        'lock_wait' => 0,
        'max_attempts' => 10,
    ];

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    public function get(string $key, $default = null, array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $startTime = microtime(true);

        try {
            $value = Cache::store($this->getStore($options))->get($key, $default);

            $this->logCacheEvent('get', $key, $value !== $default, [
                'hit' => $value !== $default,
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
            ]);

            if ($value !== $default && $options['serialize']) {
                $value = $this->unserialize($value);
            }

            return $value;
        } catch (Exception $e) {
            $this->logCacheError('get', $key, $e);
            return $default;
        }
    }

    public function put(string $key, $value, $ttl = null, array $options = []): bool
    {
        $options = array_merge($this->defaultOptions, $options);
        $ttl = $ttl ?? $options['ttl'];
        $startTime = microtime(true);

        try {
            if ($options['serialize']) {
                $value = $this->serialize($value);
            }

            if ($options['compress']) {
                $value = $this->compress($value);
            }

            $result = Cache::store($this->getStore($options))->put($key, $value, $ttl);

            $this->logCacheEvent('put', $key, $result, [
                'ttl' => $ttl,
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
                'compressed' => $options['compress'],
                'serialized' => $options['serialize'],
            ]);

            return $result;
        } catch (Exception $e) {
            $this->logCacheError('put', $key, $e);
            return false;
        }
    }

    public function forget(string $key, array $options = []): bool
    {
        $options = array_merge($this->defaultOptions, $options);
        $startTime = microtime(true);

        try {
            $result = Cache::store($this->getStore($options))->forget($key);

            $this->logCacheEvent('forget', $key, $result, [
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
            ]);

            return $result;
        } catch (Exception $e) {
            $this->logCacheError('forget', $key, $e);
            return false;
        }
    }

    public function flush(array $options = []): bool
    {
        $options = array_merge($this->defaultOptions, $options);
        $startTime = microtime(true);

        try {
            $result = Cache::store($this->getStore($options))->flush();

            $this->logCacheEvent('flush', 'all', $result, [
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
            ]);

            return $result;
        } catch (Exception $e) {
            $this->logCacheError('flush', 'all', $e);
            return false;
        }
    }

    public function remember(string $key, \Closure $callback, $ttl = null, array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $ttl = $ttl ?? $options['ttl'];
        $startTime = microtime(true);

        try {
            $value = Cache::store($this->getStore($options))->remember($key, $ttl, function () use ($callback, $key, $options) {
                $this->logCacheEvent('miss', $key, true, [
                    'store' => $this->getStore($options),
                ]);

                return $callback();
            });

            $this->logCacheEvent('remember', $key, true, [
                'ttl' => $ttl,
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
            ]);

            if ($options['serialize'] && is_string($value)) {
                $value = $this->unserialize($value);
            }

            return $value;
        } catch (Exception $e) {
            $this->logCacheError('remember', $key, $e);
            throw $e;
        }
    }

    public function rememberForever(string $key, \Closure $callback, array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $startTime = microtime(true);

        try {
            $value = Cache::store($this->getStore($options))->rememberForever($key, function () use ($callback, $key, $options) {
                $this->logCacheEvent('miss', $key, true, [
                    'store' => $this->getStore($options),
                ]);

                return $callback();
            });

            $this->logCacheEvent('remember_forever', $key, true, [
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
            ]);

            if ($options['serialize'] && is_string($value)) {
                $value = $this->unserialize($value);
            }

            return $value;
        } catch (Exception $e) {
            $this->logCacheError('remember_forever', $key, $e);
            throw $e;
        }
    }

    public function lock(string $name, int $seconds = 10, ?string $owner = null): Lock
    {
        $owner = $owner ?? Uuid::uuid4()->toString();

        return Cache::lock($name, $seconds, $owner);
    }

    public function withLock(string $key, \Closure $callback, int $timeout = 10, int $wait = 0, int $maxAttempts = 10)
    {
        $lock = $this->lock($key, $timeout);
        $attempts = 0;

        try {
            while (!$lock->get() && $attempts < $maxAttempts) {
                $attempts++;
                if ($wait > 0) {
                    usleep($wait * 1000); // Convert milliseconds to microseconds
                }
            }

            if (!$lock->get()) {
                throw new Exception("Could not acquire lock for key: {$key}");
            }

            return $callback();
        } finally {
            $lock->release();
        }
    }

    public function tags($names, array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);

        if (is_string($names)) {
            $names = [$names];
        }

        return Cache::store($this->getStore($options))->tags($names);
    }

    public function increment(string $key, $value = 1, array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $startTime = microtime(true);

        try {
            $result = Cache::store($this->getStore($options))->increment($key, $value);

            $this->logCacheEvent('increment', $key, $result, [
                'value' => $value,
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
            ]);

            return $result;
        } catch (Exception $e) {
            $this->logCacheError('increment', $key, $e);
            return false;
        }
    }

    public function decrement(string $key, $value = 1, array $options = [])
    {
        $options = array_merge($this->defaultOptions, $options);
        $startTime = microtime(true);

        try {
            $result = Cache::store($this->getStore($options))->decrement($key, $value);

            $this->logCacheEvent('decrement', $key, $result, [
                'value' => $value,
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
            ]);

            return $result;
        } catch (Exception $e) {
            $this->logCacheError('decrement', $key, $e);
            return false;
        }
    }

    public function many(array $keys, array $options = []): array
    {
        $options = array_merge($this->defaultOptions, $options);
        $startTime = microtime(true);

        try {
            $values = Cache::store($this->getStore($options))->many($keys);

            $this->logCacheEvent('many', implode(',', $keys), true, [
                'key_count' => count($keys),
                'hit_count' => count(array_filter($values)),
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
            ]);

            if ($options['serialize']) {
                $values = array_map(function ($value) {
                    return $value ? $this->unserialize($value) : $value;
                }, $values);
            }

            return $values;
        } catch (Exception $e) {
            $this->logCacheError('many', implode(',', $keys), $e);
            return array_fill_keys($keys, null);
        }
    }

    public function putMany(array $values, $ttl = null, array $options = []): bool
    {
        $options = array_merge($this->defaultOptions, $options);
        $ttl = $ttl ?? $options['ttl'];
        $startTime = microtime(true);

        try {
            if ($options['serialize']) {
                $values = array_map(function ($value) {
                    return $this->serialize($value);
                }, $values);
            }

            if ($options['compress']) {
                $values = array_map(function ($value) {
                    return $this->compress($value);
                }, $values);
            }

            $result = Cache::store($this->getStore($options))->putMany($values, $ttl);

            $this->logCacheEvent('put_many', implode(',', array_keys($values)), $result, [
                'key_count' => count($values),
                'ttl' => $ttl,
                'duration' => (microtime(true) - $startTime) * 1000,
                'store' => $this->getStore($options),
                'compressed' => $options['compress'],
                'serialized' => $options['serialize'],
            ]);

            return $result;
        } catch (Exception $e) {
            $this->logCacheError('put_many', implode(',', array_keys($values)), $e);
            return false;
        }
    }

    protected function getStore(array $options): string
    {
        return $options['store'] ?? config('cache.default');
    }

    protected function serialize($value): string
    {
        return igbinary_serialize($value);
    }

    protected function unserialize(string $value)
    {
        return igbinary_unserialize($value);
    }

    protected function compress($value): string
    {
        if (is_string($value) && strlen($value) > config('cache.compression.threshold', 1024)) {
            return gzcompress($value, config('cache.compression.level', 6));
        }
        return $value;
    }

    protected function decompress($value): string
    {
        if (is_string($value) && $this->isCompressed($value)) {
            return gzuncompress($value);
        }
        return $value;
    }

    protected function isCompressed(string $value): bool
    {
        return substr($value, 0, 2) === "\x1f\x8b";
    }

    protected function logCacheEvent(string $event, string $key, $result, array $metadata = []): void
    {
        $this->loggingService->logCacheEvent($event, $key, $result, $metadata);

        // Performance monitoring
        if (isset($metadata['duration']) && $metadata['duration'] > config('cache.monitoring.slow_operation_threshold', 100)) {
            $this->loggingService->addContext('cache_operation', [
                'event' => $event,
                'key' => $key,
                'duration' => $metadata['duration'],
            ])->logPerformanceMetric('cache_operation_time', $metadata['duration']);
        }
    }

    protected function logCacheError(string $operation, string $key, Exception $exception): void
    {
        $this->loggingService->addContext('cache_error', [
            'operation' => $operation,
            'key' => $key,
            'error' => $exception->getMessage(),
        ])->logApiError($exception);
    }

    public function getStats(array $options = []): array
    {
        $store = $this->getStore($options);

        return [
            'store' => $store,
            'default_ttl' => $this->defaultOptions['ttl'],
            'compression_enabled' => $this->defaultOptions['compress'],
            'serialization_enabled' => $this->defaultOptions['serialize'],
            'monitoring_enabled' => config('cache.monitoring.enabled', true),
        ];
    }
}
