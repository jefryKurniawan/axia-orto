<?php

namespace App\Jobs;

use App\Models\ExportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GenerateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        private readonly int $exportJobId
    ) {}

    public function handle(): void
    {
        $exportJob = ExportJob::findOrFail($this->exportJobId);

        $exportJob->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            $params = $exportJob->parameters ?? [];
            $dateFrom = $params['date_from'] ?? now()->subDays(30)->toDateString();
            $dateTo = $params['date_to'] ?? now()->toDateString();

            $rows = match ($exportJob->report_type) {
                'revenue' => $this->buildRevenueReport($dateFrom, $dateTo),
                'patients' => $this->buildPatientsReport($dateFrom, $dateTo),
                'orders' => $this->buildOrdersReport($dateFrom, $dateTo),
                'payments' => $this->buildPaymentsReport($dateFrom, $dateTo),
                default => throw new \InvalidArgumentException("Unknown report type: {$exportJob->report_type}"),
            };

            $dir = now()->format('Y/m/d');
            $filename = "{$exportJob->uuid}.csv";
            $path = "reports/{$dir}/{$filename}";

            Storage::makeDirectory("reports/{$dir}");

            $handle = Storage::disk('local')->path($path);
            $fp = fopen($handle, 'w');

            if (!empty($rows)) {
                // Header
                fputcsv($fp, array_keys($rows[0]));
                // Data
                foreach ($rows as $row) {
                    fputcsv($fp, $row);
                }
            }

            fclose($fp);

            $exportJob->update([
                'status' => 'completed',
                'file_path' => $path,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $exportJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function buildRevenueReport(string $dateFrom, string $dateTo): array
    {
        return DB::table('daily_revenue_summaries')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'Tanggal' => $row->date,
                'Total Pendapatan' => $row->total_revenue,
                'Tunai' => $row->cash_revenue,
                'Transfer' => $row->transfer_revenue,
                'Kartu' => $row->card_revenue,
                'Total Transaksi' => $row->total_transactions,
                'Transaksi Selesai' => $row->completed_transactions,
                'Transaksi Pending' => $row->pending_transactions,
            ])
            ->toArray();
    }

    private function buildPatientsReport(string $dateFrom, string $dateTo): array
    {
        return DB::table('patients')
            ->leftJoin('consultations', 'patients.id', '=', 'consultations.patient_id')
            ->select(
                'patients.medical_record_number',
                'patients.name',
                'patients.gender',
                'patients.date_of_birth',
                'patients.phone',
                'patients.insurance_type',
                DB::raw('COUNT(consultations.id) as total_consultations'),
                DB::raw('MAX(consultations.consultation_date) as last_visit')
            )
            ->whereBetween('patients.created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->groupBy(
                'patients.id',
                'patients.medical_record_number',
                'patients.name',
                'patients.gender',
                'patients.date_of_birth',
                'patients.phone',
                'patients.insurance_type'
            )
            ->orderBy('patients.name')
            ->get()
            ->map(fn ($row) => [
                'No. RM' => $row->medical_record_number,
                'Nama' => $row->name,
                'Gender' => $row->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                'Tgl Lahir' => $row->date_of_birth,
                'Telepon' => $row->phone ?? '-',
                'Asuransi' => $row->insurance_type,
                'Total Konsultasi' => $row->total_consultations,
                'Kunjungan Terakhir' => $row->last_visit ?? '-',
            ])
            ->toArray();
    }

    private function buildOrdersReport(string $dateFrom, string $dateTo): array
    {
        return DB::table('treatment_orders')
            ->join('patients', 'treatment_orders.patient_id', '=', 'patients.id')
            ->select(
                'treatment_orders.order_number',
                'treatment_orders.order_date',
                'treatment_orders.status',
                'treatment_orders.total_amount',
                'treatment_orders.delivery_date',
                'patients.name as patient_name',
                'patients.medical_record_number'
            )
            ->whereBetween('treatment_orders.order_date', [$dateFrom, $dateTo])
            ->orderBy('treatment_orders.order_date', 'desc')
            ->get()
            ->map(fn ($row) => [
                'No. Order' => $row->order_number,
                'Tanggal' => $row->order_date,
                'Pasien' => $row->patient_name,
                'No. RM' => $row->medical_record_number,
                'Total' => $row->total_amount,
                'Status' => $row->status,
                'Tgl Kirim' => $row->delivery_date ?? '-',
            ])
            ->toArray();
    }

    private function buildPaymentsReport(string $dateFrom, string $dateTo): array
    {
        return DB::table('payments')
            ->join('treatment_orders', 'payments.treatment_order_id', '=', 'treatment_orders.id')
            ->join('patients', 'treatment_orders.patient_id', '=', 'patients.id')
            ->select(
                'payments.payment_number',
                'payments.payment_date',
                'payments.amount',
                'payments.payment_method',
                'payments.status',
                'treatment_orders.order_number',
                'patients.name as patient_name'
            )
            ->whereBetween('payments.payment_date', [$dateFrom, $dateTo])
            ->orderBy('payments.payment_date', 'desc')
            ->get()
            ->map(fn ($row) => [
                'No. Pembayaran' => $row->payment_number,
                'Tanggal' => $row->payment_date,
                'Pasien' => $row->patient_name,
                'No. Order' => $row->order_number,
                'Jumlah' => $row->amount,
                'Metode' => $row->payment_method,
                'Status' => $row->status,
            ])
            ->toArray();
    }
}
