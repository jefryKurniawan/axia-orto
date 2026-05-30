<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'requested_by',
        'report_type',
        'parameters',
        'status',
        'file_path',
        'file_size',
        'row_count',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
