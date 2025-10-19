<?php
// app/Models/Traits/Cacheable.php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    protected function clearRelatedCaches(): void
    {
        $className = class_basename($this);
        Cache::tags([$className])->flush();
    }

    public function scopeCached($query, string $key, int $ttl = 300)
    {
        return Cache::tags([class_basename($this)])->remember($key, $ttl, function () use ($query) {
            return $query->get();
        });
    }

    public function scopeCachedPaginate($query, string $key, int $perPage = 15, int $ttl = 300)
    {
        $page = request()->get('page', 1);
        return Cache::tags([class_basename($this)])->remember(
            "{$key}.page.{$page}",
            $ttl,
            function () use ($query, $perPage) {
                return $query->paginate($perPage);
            }
        );
    }
}
