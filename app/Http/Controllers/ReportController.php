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

    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'patients');
        $title = 'LAPORAN DATA';
        $headers = [];
        $data = [];
        $period = 'Semua Waktu';

        if ($type === 'patients') {
            $title = 'LAPORAN DATA PASIEN';
            $headers = ['Nama Pasien', 'MRN', 'L/P', 'Usia', 'No. HP', 'Asuransi'];
            $patients = Patient::latest()->get();
            foreach ($patients as $p) {
                $data[] = [
                    $p->name,
                    $p->medical_record_number,
                    $p->gender,
                    Carbon::parse($p->date_of_birth)->age . ' Thn',
                    $p->phone,
                    $p->insurance_provider ?? 'Umum'
                ];
            }
        } elseif ($type === 'consultations') {
            $title = 'LAPORAN JADWAL KONSULTASI';
            $headers = ['Tanggal', 'Pasien', 'Dokter', 'Keluhan', 'Status'];
            $consultations = Consultation::with(['patient', 'doctor'])->latest()->get();
            foreach ($consultations as $c) {
                $data[] = [
                    Carbon::parse($c->consultation_date)->format('d/m/Y'),
                    $c->patient->name ?? '-',
                    $c->doctor->name ?? '-',
                    $c->complaint,
                    strtoupper($c->status)
                ];
            }
        } elseif ($type === 'payments') {
            $title = 'LAPORAN PENDAPATAN & PEMBAYARAN';
            $headers = ['Tanggal', 'No. Transaksi', 'Pasien', 'Metode', 'Total Bayar'];
            $payments = Payment::with('order.patient')->latest()->get();
            foreach ($payments as $p) {
                $data[] = [
                    $p->created_at->format('d/m/Y'),
                    $p->payment_number,
                    $p->order->patient->name ?? '-',
                    strtoupper($p->payment_method),
                    'Rp ' . number_format($p->amount_paid, 0, ',', '.')
                ];
            }
        }

        return view('reports.print', compact('title', 'headers', 'data', 'period'));
    }
}
