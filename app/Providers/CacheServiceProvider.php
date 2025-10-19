<?php
// app/Providers/CacheServiceProvider.php - VERSI BARU

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Tidak perlu binding repository, karena kita menggunakan Model langsung
    }

    public function boot(): void
    {
        // Clear cache ketika model created, updated, atau deleted
        $this->registerModelCacheClearing();
    }

    protected function registerModelCacheClearing(): void
    {
        // Patient cache clearing
        Event::listen([
            'eloquent.created: App\Models\Patient',
            'eloquent.updated: App\Models\Patient',
            'eloquent.deleted: App\Models\Patient',
        ], function () {
            Cache::tags(['Patient'])->flush();
            $this->commandInfo('Patient cache cleared');
        });

        // Consultation cache clearing
        Event::listen([
            'eloquent.created: App\Models\Consultation',
            'eloquent.updated: App\Models\Consultation',
            'eloquent.deleted: App\Models\Consultation',
        ], function () {
            Cache::tags(['Consultation'])->flush();
            $this->commandInfo('Consultation cache cleared');
        });

        // User cache clearing
        Event::listen([
            'eloquent.created: App\Models\User',
            'eloquent.updated: App\Models\User',
            'eloquent.deleted: App\Models\User',
        ], function () {
            Cache::tags(['User'])->flush();
            $this->commandInfo('User cache cleared');
        });

        // Service cache clearing
        Event::listen([
            'eloquent.created: App\Models\Service',
            'eloquent.updated: App\Models\Service',
            'eloquent.deleted: App\Models\Service',
        ], function () {
            Cache::tags(['Service'])->flush();
            $this->commandInfo('Service cache cleared');
        });

        // PatientMeasurement cache clearing
        Event::listen([
            'eloquent.created: App\Models\PatientMeasurement',
            'eloquent.updated: App\Models\PatientMeasurement',
            'eloquent.deleted: App\Models\PatientMeasurement',
        ], function () {
            Cache::tags(['PatientMeasurement'])->flush();
            $this->commandInfo('PatientMeasurement cache cleared');
        });

        // InventoryItem cache clearing
        Event::listen([
            'eloquent.created: App\Models\InventoryItem',
            'eloquent.updated: App\Models\InventoryItem',
            'eloquent.deleted: App\Models\InventoryItem',
        ], function () {
            Cache::tags(['InventoryItem'])->flush();
            $this->commandInfo('InventoryItem cache cleared');
        });

        // TreatmentOrder cache clearing
        Event::listen([
            'eloquent.created: App\Models\TreatmentOrder',
            'eloquent.updated: App\Models\TreatmentOrder',
            'eloquent.deleted: App\Models\TreatmentOrder',
        ], function () {
            Cache::tags(['TreatmentOrder'])->flush();
            // Also clear related caches
            Cache::tags(['OrderItem', 'ProductionTracking', 'Payment'])->flush();
            $this->commandInfo('TreatmentOrder and related caches cleared');
        });

        // OrderItem cache clearing
        Event::listen([
            'eloquent.created: App\Models\OrderItem',
            'eloquent.updated: App\Models\OrderItem',
            'eloquent.deleted: App\Models\OrderItem',
        ], function () {
            Cache::tags(['OrderItem'])->flush();
            $this->commandInfo('OrderItem cache cleared');
        });

        // ProductionTracking cache clearing
        Event::listen([
            'eloquent.created: App\Models\ProductionTracking',
            'eloquent.updated: App\Models\ProductionTracking',
            'eloquent.deleted: App\Models\ProductionTracking',
        ], function () {
            Cache::tags(['ProductionTracking'])->flush();
            $this->commandInfo('ProductionTracking cache cleared');
        });

        // Payment cache clearing
        Event::listen([
            'eloquent.created: App\Models\Payment',
            'eloquent.updated: App\Models\Payment',
            'eloquent.deleted: App\Models\Payment',
        ], function () {
            Cache::tags(['Payment'])->flush();
            $this->commandInfo('Payment cache cleared');
        });
    }

    protected function commandInfo(string $message): void
    {
        if ($this->app->runningInConsole()) {
            $this->app['log']->info("Cache: {$message}");
        }
    }
}
