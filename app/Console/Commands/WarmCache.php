<?php
// app/Console/Commands/WarmCache.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\User;
use App\Models\Service;
use App\Models\TreatmentOrder;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class WarmCache extends Command
{
    protected $signature = 'cache:warm';
    protected $description = 'Warm up application cache';

    public function handle(): void
    {
        $this->info('Starting cache warming process...');

        try {
            // Clear all cache tags first
            $this->clearAllCaches();

            // Warm Patient Caches
            $this->warmPatientCaches();

            // Warm Consultation Caches
            $this->warmConsultationCaches();

            // Warm User Caches
            $this->warmUserCaches();

            // Warm Service Caches
            $this->warmServiceCaches();

            // Warm Treatment Order Caches
            $this->warmTreatmentOrderCaches();

            // Warm Dashboard Statistics
            $this->warmDashboardStats();

            $this->info('✅ All caches warmed successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Cache warming failed: ' . $e->getMessage());
        }
    }

    protected function clearAllCaches(): void
    {
        $tags = ['Patient', 'Consultation', 'User', 'Service', 'TreatmentOrder', 'Payment'];

        foreach ($tags as $tag) {
            Cache::tags([$tag])->flush();
        }

        $this->info('🗑️  All cache tags cleared');
    }

    protected function warmPatientCaches(): void
    {
        // Patient Statistics
        Patient::getCachedStats();
        $this->info('✅ Patient statistics cached');

        // Recent Patients
        Patient::getCachedRecentPatients(30);
        $this->info('✅ Recent patients cached');

        // Patient pagination cache
        Cache::tags(['Patient'])->remember('patients.page.1', 1800, function () {
            return Patient::with(['consultations'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        });
        $this->info('✅ Patient pagination cached');
    }

    protected function warmConsultationCaches(): void
    {
        // Today's Consultations
        Consultation::getCachedTodayConsultations();
        $this->info('✅ Today consultations cached');

        // Doctor Schedules
        $doctors = User::where('role', 'dokter')->where('is_active', true)->get();
        foreach ($doctors as $doctor) {
            Consultation::getCachedDoctorSchedule($doctor->id, today());
        }
        $this->info('✅ Doctor schedules cached');

        // Consultation statistics
        Cache::tags(['Consultation'])->remember('consultation_stats', 3600, function () {
            return [
                'total' => Consultation::count(),
                'scheduled' => Consultation::where('status', 'scheduled')->count(),
                'in_progress' => Consultation::where('status', 'in_progress')->count(),
                'completed' => Consultation::where('status', 'completed')->count(),
                'today' => Consultation::whereDate('consultation_date', today())->count(),
            ];
        });
        $this->info('✅ Consultation statistics cached');
    }

    protected function warmUserCaches(): void
    {
        // Active Doctors
        User::getCachedActiveDoctors();
        $this->info('✅ Active doctors cached');

        // User Statistics
        User::getCachedUserStats();
        $this->info('✅ User statistics cached');

        // User by role cache
        $roles = ['admin', 'dokter', 'staf_klinik'];
        foreach ($roles as $role) {
            User::getCachedUsersByRole($role);
        }
        $this->info('✅ Users by role cached');
    }

    protected function warmServiceCaches(): void
    {
        // Active Services
        Cache::tags(['Service'])->remember('active_services', 86400, function () {
            return Service::where('is_active', true)
                ->orderBy('service_type')
                ->orderBy('name')
                ->get();
        });
        $this->info('✅ Active services cached');

        // Services by type
        $serviceTypes = ['konsultasi', 'ortosis', 'protesis', 'terapi', 'alat'];
        foreach ($serviceTypes as $type) {
            Cache::tags(['Service'])->remember("services.type.{$type}", 86400, function () use ($type) {
                return Service::where('service_type', $type)
                    ->where('is_active', true)
                    ->get();
            });
        }
        $this->info('✅ Services by type cached');
    }

    protected function warmTreatmentOrderCaches(): void
    {
        // Order statistics
        Cache::tags(['TreatmentOrder'])->remember('order_stats', 1800, function () {
            return [
                'total' => TreatmentOrder::count(),
                'draft' => TreatmentOrder::where('status', 'draft')->count(),
                'confirmed' => TreatmentOrder::where('status', 'confirmed')->count(),
                'production' => TreatmentOrder::where('status', 'production')->count(),
                'ready' => TreatmentOrder::where('status', 'ready')->count(),
                'delivered' => TreatmentOrder::where('status', 'delivered')->count(),
                'revenue_today' => TreatmentOrder::whereDate('order_date', today())->sum('total_amount'),
                'revenue_month' => TreatmentOrder::whereMonth('order_date', now()->month)
                    ->whereYear('order_date', now()->year)
                    ->sum('total_amount'),
            ];
        });
        $this->info('✅ Treatment order statistics cached');

        // Recent orders
        Cache::tags(['TreatmentOrder'])->remember('recent_orders', 900, function () {
            return TreatmentOrder::with(['patient', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        });
        $this->info('✅ Recent orders cached');
    }

    protected function warmDashboardStats(): void
    {
        $dashboardStats = Cache::remember('dashboard_stats', 1800, function () {
            return [
                'total_patients' => Patient::count(),
                'total_consultations_today' => Consultation::whereDate('consultation_date', today())->count(),
                'pending_orders' => TreatmentOrder::whereIn('status', ['draft', 'confirmed', 'production'])->count(),
                'monthly_revenue' => TreatmentOrder::whereMonth('order_date', now()->month)
                    ->whereYear('order_date', now()->year)
                    ->sum('total_amount'),
                'active_doctors' => User::where('role', 'dokter')->where('is_active', true)->count(),
                'low_stock_items' => \App\Models\InventoryItem::where('current_stock', '<=', \Illuminate\Support\Facades\DB::raw('min_stock'))->count(),
            ];
        });
        $this->info('✅ Dashboard statistics cached');
    }
}
