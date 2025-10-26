<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\TreatmentOrder;
use App\Models\Payment;
use App\Models\Service;
use App\Models\InventoryItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Cache stats untuk performance
        $stats = Cache::remember('dashboard_stats', 300, function () use ($today) {
            return [
                'total_patients' => Patient::count(),
                'today_consultations' => Consultation::whereDate('consultation_date', $today)->count(),
                'active_orders' => TreatmentOrder::whereIn('status', ['confirmed', 'production'])->count(),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'low_stock_items' => InventoryItem::where('current_stock', '<', DB::raw('min_stock'))->count(),
                'monthly_revenue' => Payment::whereMonth('payment_date', $today->month)
                    ->whereYear('payment_date', $today->year)
                    ->where('status', 'completed')
                    ->sum('amount')
            ];
        });

        $recentPatients = Patient::latest()->take(5)->get();
        $todaySchedule = Consultation::with(['patient', 'doctor'])
            ->whereDate('consultation_date', $today)
            ->orderBy('consultation_date')
            ->get();

        $pendingOrders = TreatmentOrder::with(['patient'])
            ->whereIn('status', ['draft', 'confirmed'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentPatients', 'todaySchedule', 'pendingOrders'));
    }

    public function cleopatraDashboard()
    {
        return view('cleopatra-dashboard');
    }
}
