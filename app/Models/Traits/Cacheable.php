<?php
// app/Models/Traits/Cacheable.php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait Cacheable
{
    protected static function bootCacheable(): void
    {
        static::created(function (Model $model) {
            $model->clearRelatedCaches();
        });

        static::updated(function (Model $model) {
            $model->clearRelatedCaches();
        });

        static::deleted(function (Model $model) {
            $model->clearRelatedCaches();
        });
    }

    /**
     * Check if cache store supports tags
     */
    protected function cacheSupportsTags(): bool
    {
        try {
            $store = Cache::getStore();
            $driver = config('cache.default');

            // Driver yang support tags: redis, memcached
            $tagSupportedDrivers = ['redis', 'memcached', 'array'];

            return method_exists($store, 'tags') &&
                in_array($driver, $tagSupportedDrivers);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get cache tag for this model
     */
    protected function getCacheTag(): string
    {
        // Gunakan full class name untuk menghindari conflict
        return 'model_' . str_replace('\\', '_', get_class($this));
    }

    /**
     * Get cache key dengan prefix untuk menghindari conflict
     */
    protected function getCacheKey(string $key): string
    {
        $tag = $this->getCacheTag();
        return "{$tag}.{$key}";
    }

    protected function clearRelatedCaches(): void
    {
        try {
            $tag = $this->getCacheTag();

            if ($this->cacheSupportsTags()) {
                Cache::tags([$tag])->flush();
                Log::info("Cache flushed for tag: {$tag}");
            } else {
                // Fallback: Clear manual menggunakan pattern (hanya untuk Redis)
                $this->clearRedisCacheByPattern($this->getCacheKey('*'));
            }
        } catch (\Exception $e) {
            Log::warning('Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear Redis cache by pattern (hanya untuk Redis driver)
     */
    protected function clearRedisCacheByPattern(string $pattern): void
    {
        try {
            $driver = config('cache.default');

            if ($driver !== 'redis') {
                return; // Hanya untuk Redis
            }

            $redis = Cache::getRedis();

            // Gunakan SCAN untuk pattern matching yang lebih aman
            $iterator = null;
            $matches = [];

            do {
                // SCAN dengan pattern
                $result = $redis->scan($iterator, ['match' => $pattern, 'count' => 100]);
                $matches = is_array($result) ? $result : [];

                if (!empty($matches)) {
                    $redis->del($matches);
                }
            } while ($iterator > 0);

            Log::info("Redis cache cleared for pattern: {$pattern}");
        } catch (\Exception $e) {
            Log::warning('Redis pattern cache clearing failed: ' . $e->getMessage());
        }
    }

    /**
     * Alternative: Clear cache dengan menyimpan daftar keys
     */
    protected function clearCacheWithKeyTracking(): void
    {
        try {
            $tag = $this->getCacheTag();
            $keysListKey = "{$tag}_keys_list";

            // Ambil daftar keys yang pernah dibuat untuk model ini
            $keys = Cache::get($keysListKey, []);

            // Hapus semua keys
            foreach ($keys as $key) {
                Cache::forget($key);
            }

            // Hapus daftar keys
            Cache::forget($keysListKey);

            Log::info("Cleared {$keysListKey} with " . count($keys) . " keys");
        } catch (\Exception $e) {
            Log::warning('Key tracking cache clearing failed: ' . $e->getMessage());
        }
    }

    /**
     * Track cache key untuk model ini
     */
    protected function trackCacheKey(string $key): void
    {
        try {
            $tag = $this->getCacheTag();
            $keysListKey = "{$tag}_keys_list";

            $keys = Cache::get($keysListKey, []);
            $keys[] = $key;

            // Simpan daftar keys unik
            $keys = array_unique($keys);
            Cache::forever($keysListKey, $keys);
        } catch (\Exception $e) {
            // Skip tracking jika error
        }
    }

    public function scopeCached($query, string $key, int $ttl = 300)
    {
        $cacheKey = $this->getCacheKey($key);
        $tags = [$this->getCacheTag()];

        try {
            // Track key untuk fallback clearing
            $this->trackCacheKey($cacheKey);

            if ($this->cacheSupportsTags()) {
                return Cache::tags($tags)->remember($cacheKey, $ttl, function () use ($query) {
                    return $query->get();
                });
            } else {
                // Fallback tanpa tags
                return Cache::remember($cacheKey, $ttl, function () use ($query) {
                    return $query->get();
                });
            }
        } catch (\Exception $e) {
            // Jika cache gagal, return query biasa
            Log::warning('Cache failed, using direct query: ' . $e->getMessage());
            return $query->get();
        }
    }

    public function scopeCachedPaginate($query, string $key, int $perPage = 15, int $ttl = 300)
    {
        $page = request()->get('page', 1);
        $cacheKey = $this->getCacheKey("{$key}.page.{$page}");
        $tags = [$this->getCacheTag()];

        try {
            // Track key untuk fallback clearing
            $this->trackCacheKey($cacheKey);

            if ($this->cacheSupportsTags()) {
                return Cache::tags($tags)->remember(
                    $cacheKey,
                    $ttl,
                    function () use ($query, $perPage) {
                        return $query->paginate($perPage);
                    }
                );
            } else {
                return Cache::remember(
                    $cacheKey,
                    $ttl,
                    function () use ($query, $perPage) {
                        return $query->paginate($perPage);
                    }
                );
            }
        } catch (\Exception $e) {
            Log::warning('Cache failed, using direct pagination: ' . $e->getMessage());
            return $query->paginate($perPage);
        }
    }

    /**
     * Method baru untuk cached find
     */
    public static function cachedFind($id, int $ttl = 300)
    {
        $model = new static();
        $cacheKey = $model->getCacheKey("find.{$id}");
        $tags = [$model->getCacheTag()];

        try {
            // Track key untuk fallback clearing
            $model->trackCacheKey($cacheKey);

            if ($model->cacheSupportsTags()) {
                return Cache::tags($tags)->remember($cacheKey, $ttl, function () use ($id) {
                    return static::find($id);
                });
            } else {
                return Cache::remember($cacheKey, $ttl, function () use ($id) {
                    return static::find($id);
                });
            }
        } catch (\Exception $e) {
            Log::warning('Cache failed for find: ' . $e->getMessage());
            return static::find($id);
        }
    }

    /**
     * Manual cache clearing method
     */
    public static function clearAllCache(): void
    {
        try {
            $model = new static();
            $model->clearRelatedCaches();
        } catch (\Exception $e) {
            Log::warning('Manual cache clearing failed: ' . $e->getMessage());
        }
    }
}
