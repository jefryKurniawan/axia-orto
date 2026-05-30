<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        CacheHelper::bumpVersion('payments');
        $this->updateRevenueSummary($payment->payment_date->toDateString());
    }

    public function updated(Payment $payment): void
    {
        CacheHelper::bumpVersion('payments');
        $oldDate = $payment->getOriginal('payment_date');
        $newDate = $payment->payment_date->toDateString();

        if ($oldDate !== $newDate) {
            $this->updateRevenueSummary(\Carbon\Carbon::parse($oldDate)->toDateString());
        }
        $this->updateRevenueSummary($newDate);
    }

    public function deleted(Payment $payment): void
    {
        CacheHelper::bumpVersion('payments');
        $this->updateRevenueSummary($payment->payment_date->toDateString());
    }

    private function updateRevenueSummary(string $date): void
    {
        $stats = DB::table('payments')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as total_revenue,
                COALESCE(SUM(CASE WHEN status = 'completed' AND payment_method = 'cash' THEN amount ELSE 0 END), 0) as cash_revenue,
                COALESCE(SUM(CASE WHEN status = 'completed' AND payment_method = 'transfer' THEN amount ELSE 0 END), 0) as transfer_revenue,
                COALESCE(SUM(CASE WHEN status = 'completed' AND payment_method IN ('debit_card', 'credit_card') THEN amount ELSE 0 END), 0) as card_revenue,
                COUNT(*) as total_transactions,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_transactions,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_transactions
            ")
            ->whereDate('payment_date', $date)
            ->first();

        DB::table('daily_revenue_summaries')
            ->updateOrInsert(
                ['date' => $date],
                [
                    'total_revenue' => $stats->total_revenue ?? 0,
                    'cash_revenue' => $stats->cash_revenue ?? 0,
                    'transfer_revenue' => $stats->transfer_revenue ?? 0,
                    'card_revenue' => $stats->card_revenue ?? 0,
                    'total_transactions' => $stats->total_transactions ?? 0,
                    'completed_transactions' => $stats->completed_transactions ?? 0,
                    'pending_transactions' => $stats->pending_transactions ?? 0,
                ]
            );
    }
}
