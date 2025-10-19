<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected $model;
    protected $cacheKey;
    protected $cacheTtl = 300;

    protected function cachedResponse($key, $callback, $tags = [], $ttl = null)
    {
        $ttl = $ttl ?? $this->cacheTtl;
        $cacheKey = "{$this->cacheKey}.{$key}";

        // ✅ FIX: Check if cache store supports tags
        try {
            if (!empty($tags) && method_exists(Cache::getStore(), 'tags')) {
                return Cache::tags($tags)->remember($cacheKey, $ttl, $callback);
            } else {
                return Cache::remember($cacheKey, $ttl, $callback);
            }
        } catch (\Exception $e) {
            // ✅ Fallback: langsung execute callback tanpa cache
            return $callback();
        }
    }

    protected function clearCache($tags = [])
    {
        if (!empty($tags)) {
            try {
                if (method_exists(Cache::getStore(), 'tags')) {
                    Cache::tags($tags)->flush();
                }
            } catch (\Exception $e) {
                // Skip cache clearing if tags not supported
            }
        }
    }

    protected function successResponse($data, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message, $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }
}
