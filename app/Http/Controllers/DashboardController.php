<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\TreatmentOrder;
use App\Models\Payment;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();
        $weekStart = $today->copy()->startOfWeek();
        $monthStart = $today->copy()->startOfMonth();

        // Cache stats untuk performance
        $stats = Cache::remember('dashboard_stats', 300, function () use ($today, $weekStart, $monthStart) {
            // Handle low_stock_items dengan try-catch jika tabel belum ada
            $lowStockItems = 0;
            try {
                $lowStockItems = InventoryItem::whereRaw('current_stock < minimum_stock')->count();
            } catch (\Exception $e) {
                $lowStockItems = 0;
            }

            $pendingConsultations = 0;
            $pendingOrders = 0;
            try {
                $pendingConsultations = Consultation::where('status', 'scheduled')->count();
                $pendingOrders = TreatmentOrder::where('status', 'pending')->count();
            } catch (\Exception $e) {
            }

            return [
                'total_patients' => Patient::count(),
                'today_consultations' => Consultation::whereDate('consultation_date', $today)->count(),
                'week_consultations' => Consultation::whereBetween('consultation_date', [$weekStart, $today])->count(),
                'month_consultations' => Consultation::whereBetween('consultation_date', [$monthStart, $today])->count(),
                'active_orders' => TreatmentOrder::whereIn('status', ['pending', 'in_progress'])->count(),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'pending_actions' => $pendingConsultations + $pendingOrders,
                'low_stock_items' => $lowStockItems,
                'monthly_revenue' => Payment::whereMonth('payment_date', $today->month)
                    ->whereYear('payment_date', $today->year)
                    ->where('status', 'paid')
                    ->sum('amount')
            ];
        });

        // Ambil data yang diperlukan untuk dashboard
        $recentPatients = Patient::latest()->take(5)->get();

        // Konsultasi terbaru
        $recentConsultations = collect();
        try {
            $recentConsultations = Consultation::with(['patient', 'doctor'])
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Tabel consultations mungkin belum ada
        }

        // Jadwal hari ini
        $todaySchedule = collect();
        try {
            $todaySchedule = Consultation::with(['patient', 'doctor'])
                ->whereDate('consultation_date', $today)
                ->orderBy('consultation_date', 'asc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Tabel consultations mungkin belum ada
        }

        // Jadwal besok
        $tomorrowSchedule = collect();
        try {
            $tomorrowSchedule = Consultation::with(['patient', 'doctor'])
                ->whereDate('consultation_date', $tomorrow)
                ->orderBy('consultation_date', 'asc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Tabel consultations mungkin belum ada
        }

        // Tindak lanjut (follow-ups)
        $followUps = collect();
        try {
            $followUps = Consultation::with(['patient'])
                ->where('status', 'completed')
                ->whereNotNull('follow_up_date')
                ->whereDate('follow_up_date', '>', $today)
                ->orderBy('follow_up_date', 'asc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Tabel consultations mungkin belum ada
        }

        // Pemesanan yang menunggu
        $pendingOrders = collect();
        try {
            $pendingOrders = TreatmentOrder::with(['patient'])
                ->whereIn('status', ['draft', 'confirmed'])
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Tabel treatment_orders mungkin belum adaa
        }

        return view('dashboard', [
            'stats' => $stats,
            'recentPatients' => $recentPatients,
            'recentConsultations' => $recentConsultations,
            'todaySchedule' => $todaySchedule,
            'tomorrowSchedule' => $tomorrowSchedule,
            'followUps' => $followUps,
            'pendingOrders' => $pendingOrders,
        ]);
    }
}
