<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\TreatmentOrder;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function daily()
    {
        $date = request('date', Carbon::today()->toDateString());
        $selectedDate = Carbon::parse($date);

        $stats = [
            'consultations' => Consultation::whereDate('consultation_date', $selectedDate)->count(),
            'new_patients' => Patient::whereDate('created_at', $selectedDate)->count(),
            'new_orders' => TreatmentOrder::whereDate('order_date', $selectedDate)->count(),
            'payments' => Payment::whereDate('payment_date', $selectedDate)
                ->where('status', 'completed')
                ->sum('amount')
        ];

        return view('reports.daily', compact('stats', 'selectedDate'));
    }


    public function monthly()
    {
        $month = request('month', Carbon::now()->month);
        $year = request('year', Carbon::now()->year);

        $stats = [
            'total_consultations' => Consultation::whereYear('consultation_date', $year)
                ->whereMonth('consultation_date', $month)
                ->count(),
            'new_patients' => Patient::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count(),
            'total_orders' => TreatmentOrder::whereYear('order_date', $year)
                ->whereMonth('order_date', $month)
                ->count(),
            'total_revenue' => Payment::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $month)
                ->where('status', 'completed')
                ->sum('amount')
        ];

        // Monthly trend data
        $monthlyTrends = Payment::select(
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('SUM(amount) as total')
        )
            ->whereYear('payment_date', $year)
            ->where('status', 'completed')
            ->groupBy('month')
            ->get();

        return view('reports.monthly', compact('stats', 'month', 'year', 'monthlyTrends'));
    }

    public function patients()
    {
        $patients = Patient::withCount(['consultations', 'treatmentOrders'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Patient::count(),
            'with_consultations' => Patient::has('consultations')->count(),
            'with_orders' => Patient::has('treatmentOrders')->count(),
        ];

        return view('reports.patients', compact('patients', 'stats'));
    }

    public function revenue()
    {
        $startDate = request('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = request('end_date', Carbon::today()->toDateString());

        $revenueData = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                'payment_method',
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('payment_method')
            ->get();

        $totalRevenue = $revenueData->sum('total');
        $totalTransactions = $revenueData->sum('count');

        return view('reports.revenue', compact('revenueData', 'totalRevenue', 'totalTransactions', 'startDate', 'endDate'));
    }
}
