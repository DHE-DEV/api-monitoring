<?php
// app/Models/User.php - Erweitert für Laravel 12

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Accessor für vollen Namen
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: $this->name;
    }

    // Beziehung zu API Monitors (falls ApiMonitor Model existiert)
    public function apiMonitors()
    {
        return $this->hasMany(\App\Models\ApiMonitor::class, 'created_by');
    }

    // Hilfsmethode für aktive Benutzer
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Hilfsmethode für Benutzer mit bestimmter Rolle
    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }
}
