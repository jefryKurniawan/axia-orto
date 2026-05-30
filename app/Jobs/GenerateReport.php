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

            $dir = now()->format('Y/m/d');
            $filename = "{$exportJob->uuid}.csv";
            $path = "reports/{$dir}/{$filename}";

            Storage::makeDirectory("reports/{$dir}");

            $filePath = Storage::disk('local')->path($path);
            $fp = fopen($filePath, 'w');

            $headerWritten = false;
            $rowCount = 0;

            match ($exportJob->report_type) {
                'revenue' => $this->streamRevenueReport($fp, $dateFrom, $dateTo, $headerWritten, $rowCount),
                'patients' => $this->streamPatientsReport($fp, $dateFrom, $dateTo, $headerWritten, $rowCount),
                'orders' => $this->streamOrdersReport($fp, $dateFrom, $dateTo, $headerWritten, $rowCount),
                'payments' => $this->streamPaymentsReport($fp, $dateFrom, $dateTo, $headerWritten, $rowCount),
                default => throw new \InvalidArgumentException("Unknown report type: {$exportJob->report_type}"),
            };

            fclose($fp);

            $exportJob->update([
                'status' => 'completed',
                'file_path' => $path,
                'file_size' => Storage::disk('local')->size($path),
                'row_count' => $rowCount,
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

    private function streamRevenueReport($fp, string $dateFrom, string $dateTo, bool &$headerWritten, int &$rowCount): void
    {
        $header = ['Tanggal', 'Total Pendapatan', 'Tunai', 'Transfer', 'Kartu', 'Total Transaksi', 'Transaksi Selesai', 'Transaksi Pending'];

        DB::table('daily_revenue_summaries')
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date')
            ->cursor()
            ->each(function ($row) use ($fp, $header, &$headerWritten, &$rowCount) {
                if (!$headerWritten) {
                    fputcsv($fp, $header, ',', '"', '');
                    $headerWritten = true;
                }
                fputcsv($fp, [
                    $row->date, $row->total_revenue, $row->cash_revenue, $row->transfer_revenue,
                    $row->card_revenue, $row->total_transactions, $row->completed_transactions, $row->pending_transactions,
                ], ',', '"', '');
                $rowCount++;
            });
    }

    private function streamPatientsReport($fp, string $dateFrom, string $dateTo, bool &$headerWritten, int &$rowCount): void
    {
        $header = ['No. RM', 'Nama', 'Gender', 'Tgl Lahir', 'Telepon', 'Asuransi', 'Total Konsultasi', 'Kunjungan Terakhir'];

        DB::table('patients')
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
            ->cursor()
            ->each(function ($row) use ($fp, $header, &$headerWritten, &$rowCount) {
                if (!$headerWritten) {
                    fputcsv($fp, $header, ',', '"', '');
                    $headerWritten = true;
                }
                fputcsv($fp, [
                    $row->medical_record_number, $row->name,
                    $row->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                    $row->date_of_birth, $row->phone ?? '-', $row->insurance_type,
                    $row->total_consultations, $row->last_visit ?? '-',
                ], ',', '"', '');
                $rowCount++;
            });
    }

    private function streamOrdersReport($fp, string $dateFrom, string $dateTo, bool &$headerWritten, int &$rowCount): void
    {
        $header = ['No. Order', 'Tanggal', 'Pasien', 'No. RM', 'Total', 'Status', 'Tgl Kirim'];

        DB::table('treatment_orders')
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
            ->cursor()
            ->each(function ($row) use ($fp, $header, &$headerWritten, &$rowCount) {
                if (!$headerWritten) {
                    fputcsv($fp, $header, ',', '"', '');
                    $headerWritten = true;
                }
                fputcsv($fp, [
                    $row->order_number, $row->order_date, $row->patient_name,
                    $row->medical_record_number, $row->total_amount, $row->status,
                    $row->delivery_date ?? '-',
                ], ',', '"', '');
                $rowCount++;
            });
    }

    private function streamPaymentsReport($fp, string $dateFrom, string $dateTo, bool &$headerWritten, int &$rowCount): void
    {
        $header = ['No. Pembayaran', 'Tanggal', 'Jumlah', 'Metode', 'Status', 'No. Order', 'Pasien'];

        DB::table('payments')
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
            ->cursor()
            ->each(function ($row) use ($fp, $header, &$headerWritten, &$rowCount) {
                if (!$headerWritten) {
                    fputcsv($fp, $header, ',', '"', '');
                    $headerWritten = true;
                }
                fputcsv($fp, [
                    $row->payment_number, $row->payment_date, $row->amount,
                    $row->payment_method, $row->status, $row->order_number, $row->patient_name,
                ], ',', '"', '');
                $rowCount++;
            });
    }
}
