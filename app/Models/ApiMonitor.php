<?php
// app/Models/ApiMonitor.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiMonitor extends Model
{
    use HasFactory;

    // Timestamps aktivieren (Standard in Laravel)
    public $timestamps = true;

    protected $fillable = [
        'name',
        'url',
        'method',
        'headers',
        'payload',
        'interval_minutes',
        'is_active',
        'email_alerts_enabled',
        'email_alerts_disabled_at',
        'email_alerts_disabled_by',
        'email_alerts_disabled_reason',
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'is_active' => 'boolean',
        'email_alerts_enabled' => 'boolean',
        'email_alerts_disabled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function results()
    {
        return $this->hasMany(ApiMonitorResult::class);
    }

    public function latestResult()
    {
        return $this->hasOne(ApiMonitorResult::class)->latestOfMany();
    }

    public function disableEmailAlerts(string $reason = '', string $disabledBy = 'Manual')
    {
        $this->update([
            'email_alerts_enabled' => false,
            'email_alerts_disabled_at' => now(),
            'email_alerts_disabled_by' => $disabledBy,
            'email_alerts_disabled_reason' => $reason,
        ]);
    }

    public function enableEmailAlerts()
    {
        $this->update([
            'email_alerts_enabled' => true,
            'email_alerts_disabled_at' => null,
            'email_alerts_disabled_by' => null,
            'email_alerts_disabled_reason' => null,
        ]);
    }

    public function getEmailAlertsStatusAttribute()
    {
        if (!$this->email_alerts_enabled) {
            $duration = $this->email_alerts_disabled_at ?
                $this->email_alerts_disabled_at->diffForHumans() : '';
            return "Deaktiviert {$duration}";
        }
        return 'Aktiv';
    }
}
