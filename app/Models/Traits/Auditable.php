<?php

namespace App\Models\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->audit('created', [], $model->getAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (!empty($dirty)) {
                $old = [];
                $new = [];
                foreach ($dirty as $key => $value) {
                    $old[$key] = $model->getOriginal($key);
                    $new[$key] = $value;
                }
                $model->audit('updated', $old, $new);
            }
        });

        static::deleted(function ($model) {
            $model->audit('deleted', $model->getOriginal(), []);
        });
    }

    protected function audit(string $event, array $old, array $new): AuditLog
    {
        // Filter out non-scalar values
        $old = array_filter($old, fn ($v) => is_scalar($v) || is_null($v));
        $new = array_filter($new, fn ($v) => is_scalar($v) || is_null($v));

        return AuditLog::create([
            'user_id' => auth()->id(),
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
