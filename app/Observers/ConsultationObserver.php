<?php

namespace App\Observers;

use App\Helpers\CacheHelper;
use App\Models\Consultation;
use Illuminate\Support\Facades\DB;

class ConsultationObserver
{
    public function created(Consultation $consultation): void
    {
        CacheHelper::bumpVersion('consultations');
        $this->updateSummary($consultation->consultation_date->toDateString());
    }

    public function updated(Consultation $consultation): void
    {
        CacheHelper::bumpVersion('consultations');
        $oldDate = $consultation->getOriginal('consultation_date');
        $newDate = $consultation->consultation_date->toDateString();

        if ($oldDate !== $newDate) {
            $this->updateSummary(\Carbon\Carbon::parse($oldDate)->toDateString());
        }
        $this->updateSummary($newDate);
    }

    public function deleted(Consultation $consultation): void
    {
        CacheHelper::bumpVersion('consultations');
        $this->updateSummary($consultation->consultation_date->toDateString());
    }

    private function updateSummary(string $date): void
    {
        $stats = DB::table('consultations')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            ")
            ->whereDate('consultation_date', $date)
            ->first();

        DB::table('daily_consultation_summaries')
            ->updateOrInsert(
                ['date' => $date],
                [
                    'total' => $stats->total ?? 0,
                    'scheduled' => $stats->scheduled ?? 0,
                    'in_progress' => $stats->in_progress ?? 0,
                    'completed' => $stats->completed ?? 0,
                    'cancelled' => $stats->cancelled ?? 0,
                ]
            );
    }
}
