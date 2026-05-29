<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheHelper
{
    /**
     * Get current version for a module.
     */
    public static function getVersion(string $module): int
    {
        $row = DB::table('cache_versions')
            ->where('module_name', $module)
            ->first();

        return $row ? (int) $row->version : 1;
    }

    /**
     * Increment version for a module (invalidates all caches for that module).
     */
    public static function bumpVersion(string $module): void
    {
        DB::table('cache_versions')
            ->where('module_name', $module)
            ->update(['version' => DB::raw('version + 1')]);

        // If module doesn't exist yet, create it
        if (DB::table('cache_versions')->where('module_name', $module)->count() === 0) {
            DB::table('cache_versions')->insert([
                'module_name' => $module,
                'version' => 1,
            ]);
        }
    }

    /**
     * Remember with versioned cache key.
     *
     * Key pattern: {module}.{action}.{params}.v{version}
     * Example: patients.list.page_1_search_.v2
     */
    public static function remember(string $key, int $ttl, callable $callback)
    {
        // Extract module name from key (first segment before dot)
        $module = explode('.', $key)[0];
        $version = self::getVersion($module);
        $versionedKey = "{$key}.v{$version}";

        return Cache::remember($versionedKey, $ttl, $callback);
    }

    /**
     * Flush all cache for a module by bumping its version.
     */
    public static function flushModule(string $module): void
    {
        self::bumpVersion($module);
    }

    /**
     * Build a cache key with consistent formatting.
     *
     * Example: CacheHelper::key('patients', 'list', ['page' => 1, 'search' => 'john'])
     * Returns: patients.list.page_1_search_john
     */
    public static function key(string $module, string $action, array $params = []): string
    {
        $parts = ["{$module}.{$action}"];

        foreach ($params as $k => $v) {
            if ($v !== null && $v !== '') {
                $parts[] = "{$k}_" . urlencode((string) $v);
            }
        }

        return implode('.', $parts);
    }
}
