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
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'is_active' => 'boolean',
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
}
