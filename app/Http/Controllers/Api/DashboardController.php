<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $cacheKey = CacheHelper::key('dashboard', 'stats');

        $stats = CacheHelper::remember($cacheKey, 300, function () {
            $today = now()->toDateString();

            // Today's consultation summary
            $todaySummary = DB::table('daily_consultation_summaries')
                ->where('date', $today)
                ->first();

            // Total patients
            $totalPatients = DB::table('patients')->count();

            // Active doctors
            $activeDoctors = DB::table('users')
                ->where('role', 'dokter')
                ->where('is_active', true)
                ->count();

            // New patients this month
            $newPatientsMonth = DB::table('patients')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // Recent consultations (last 5)
            $recentConsultations = DB::table('consultations')
                ->join('patients', 'consultations.patient_id', '=', 'patients.id')
                ->join('users', 'consultations.doctor_id', '=', 'users.id')
                ->select(
                    'consultations.uuid', 'consultations.consultation_date',
                    'consultations.complaint', 'consultations.status',
                    'patients.name as patient_name',
                    'users.name as doctor_name'
                )
                ->orderBy('consultations.consultation_date', 'desc')
                ->limit(5)
                ->get();

            // Low stock items
            $lowStockCount = DB::table('inventory_items')
                ->whereRaw('quantity <= reorder_level')
                ->where('is_active', true)
                ->count();

            $lowStockItems = DB::table('inventory_items')
                ->select('uuid', 'name', 'code', 'quantity', 'reorder_level', 'unit')
                ->whereRaw('quantity <= reorder_level')
                ->where('is_active', true)
                ->orderByRaw('(quantity / NULLIF(reorder_level, 0))')
                ->limit(5)
                ->get();

            // Analytics: last 30 days trends
            $thirtyDaysAgo = now()->subDays(30)->toDateString();

            $revenueTrend = DB::table('daily_revenue_summaries')
                ->select('date', 'total_revenue', 'total_transactions')
                ->where('date', '>=', $thirtyDaysAgo)
                ->orderBy('date')
                ->get();

            $consultationTrend = DB::table('daily_consultation_summaries')
                ->select('date', 'total', 'completed', 'cancelled')
                ->where('date', '>=', $thirtyDaysAgo)
                ->orderBy('date')
                ->get();

            // Order status distribution
            $orderStatusDistribution = DB::table('treatment_orders')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get();

            // Production pipeline
            $productionPipeline = DB::table('production_trackings')
                ->select('step', 'status', DB::raw('COUNT(*) as count'))
                ->groupBy('step', 'status')
                ->get();

            return [
                'today' => [
                    'total' => $todaySummary->total ?? 0,
                    'scheduled' => $todaySummary->scheduled ?? 0,
                    'in_progress' => $todaySummary->in_progress ?? 0,
                    'completed' => $todaySummary->completed ?? 0,
                    'cancelled' => $todaySummary->cancelled ?? 0,
                ],
                'total_patients' => $totalPatients,
                'active_doctors' => $activeDoctors,
                'new_patients_month' => $newPatientsMonth,
                'recent_consultations' => $recentConsultations,
                'low_stock_count' => $lowStockCount,
                'low_stock_items' => $lowStockItems,
                'revenue_trend' => $revenueTrend,
                'consultation_trend' => $consultationTrend,
                'order_status_distribution' => $orderStatusDistribution,
                'production_pipeline' => $productionPipeline,
            ];
        });

        return response()->json(['success' => true, 'data' => $stats]);
    }
}
