<?php
// app/Providers/CacheServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Tidak perlu binding repository
    }

    public function boot(): void
    {
        $this->registerModelCacheClearing();
    }

    protected function registerModelCacheClearing(): void
    {
        $this->registerPatientCacheClearing();
        $this->registerConsultationCacheClearing();
        $this->registerUserCacheClearing();
        $this->registerServiceCacheClearing();
        $this->registerPatientMeasurementCacheClearing();
        $this->registerInventoryItemCacheClearing();
        $this->registerTreatmentOrderCacheClearing();
        $this->registerOrderItemCacheClearing();
        $this->registerProductionTrackingCacheClearing();
        $this->registerPaymentCacheClearing();
    }

    protected function registerPatientCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\Patient',
            'eloquent.updated: App\Models\Patient',
            'eloquent.deleted: App\Models\Patient',
        ], function () {
            $this->flushCacheByTags(['Patient']);
            $this->commandInfo('Patient cache cleared');
        });
    }

    protected function registerConsultationCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\Consultation',
            'eloquent.updated: App\Models\Consultation',
            'eloquent.deleted: App\Models\Consultation',
        ], function () {
            $this->flushCacheByTags(['Consultation']);
            $this->commandInfo('Consultation cache cleared');
        });
    }

    protected function registerUserCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\User',
            'eloquent.updated: App\Models\User',
            'eloquent.deleted: App\Models\User',
        ], function () {
            $this->flushCacheByTags(['User']);
            $this->commandInfo('User cache cleared');
        });
    }

    protected function registerServiceCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\Service',
            'eloquent.updated: App\Models\Service',
            'eloquent.deleted: App\Models\Service',
        ], function () {
            $this->flushCacheByTags(['Service']);
            $this->commandInfo('Service cache cleared');
        });
    }

    protected function registerPatientMeasurementCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\PatientMeasurement',
            'eloquent.updated: App\Models\PatientMeasurement',
            'eloquent.deleted: App\Models\PatientMeasurement',
        ], function () {
            $this->flushCacheByTags(['PatientMeasurement']);
            $this->commandInfo('PatientMeasurement cache cleared');
        });
    }

    protected function registerInventoryItemCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\InventoryItem',
            'eloquent.updated: App\Models\InventoryItem',
            'eloquent.deleted: App\Models\InventoryItem',
        ], function () {
            $this->flushCacheByTags(['InventoryItem']);
            $this->commandInfo('InventoryItem cache cleared');
        });
    }

    protected function registerTreatmentOrderCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\TreatmentOrder',
            'eloquent.updated: App\Models\TreatmentOrder',
            'eloquent.deleted: App\Models\TreatmentOrder',
        ], function () {
            $this->flushCacheByTags(['TreatmentOrder', 'OrderItem', 'ProductionTracking', 'Payment']);
            $this->commandInfo('TreatmentOrder and related caches cleared');
        });
    }

    protected function registerOrderItemCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\OrderItem',
            'eloquent.updated: App\Models\OrderItem',
            'eloquent.deleted: App\Models\OrderItem',
        ], function () {
            $this->flushCacheByTags(['OrderItem']);
            $this->commandInfo('OrderItem cache cleared');
        });
    }

    protected function registerProductionTrackingCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\ProductionTracking',
            'eloquent.updated: App\Models\ProductionTracking',
            'eloquent.deleted: App\Models\ProductionTracking',
        ], function () {
            $this->flushCacheByTags(['ProductionTracking']);
            $this->commandInfo('ProductionTracking cache cleared');
        });
    }

    protected function registerPaymentCacheClearing(): void
    {
        Event::listen([
            'eloquent.created: App\Models\Payment',
            'eloquent.updated: App\Models\Payment',
            'eloquent.deleted: App\Models\Payment',
        ], function () {
            $this->flushCacheByTags(['Payment']);
            $this->commandInfo('Payment cache cleared');
        });
    }

    protected function flushCacheByTags(array $tags): void
    {
        try {
            // Cek apakah driver support tagging
            $store = Cache::getStore();
            if (method_exists($store, 'tags')) {
                Cache::tags($tags)->flush();
            } else {
                // Fallback: flush seluruh cache jika tidak support tagging
                Cache::flush();
                $this->commandInfo('Cache driver does not support tagging, flushed entire cache');
            }
        } catch (\Exception $e) {
            $this->commandInfo("Cache flush error: {$e->getMessage()}");
            // Fallback ke manual cache clearing
            $this->manualCacheClear($tags);
        }
    }

    protected function manualCacheClear(array $tags): void
    {
        foreach ($tags as $tag) {
            $this->clearCacheByPattern(strtolower($tag));
        }
    }

    protected function clearCacheByPattern(string $pattern): void
    {
        try {
            $redis = Cache::getRedis();
            $keys = $redis->keys("*{$pattern}*");
            foreach ($keys as $key) {
                // Remove prefix jika ada
                $key = str_replace(config('cache.prefix'), '', $key);
                Cache::forget($key);
            }
            $this->commandInfo("Manually cleared cache for pattern: {$pattern}");
        } catch (\Exception $e) {
            $this->commandInfo("Manual cache clear failed: {$e->getMessage()}");
        }
    }

    protected function commandInfo(string $message): void
    {
        if ($this->app->runningInConsole()) {
            $this->app['log']->info("Cache: {$message}");
        }
    }
}
