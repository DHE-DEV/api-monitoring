<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiMonitorResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_monitor_id',
        'response_time_ms',
        'status_code',
        'success',
        'error_message',
        'response_body',
        'executed_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'response_body' => 'array',
        'executed_at' => 'datetime',
    ];

    public function apiMonitor()
    {
        return $this->belongsTo(ApiMonitor::class);
    }
}
