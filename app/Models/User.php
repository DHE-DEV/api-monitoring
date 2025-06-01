<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'department',
        'phone',
        'avatar',
        'is_active',
        'email_verified_at',
        'email_notifications',
        'notification_types',
        'monitor_access',
        'password_changed_at',
        'last_login_at',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
            'notification_types' => 'array',
            'monitor_access' => 'array',
        ];
    }

    /**
     * Check if user is Super Admin
     * Verwendet Spatie's hasRole() Methode
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin') || $this->hasRole('superadmin');
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->name;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get all permissions for this user (direct + via roles)
     */
    public function getAllPermissions()
    {
        return $this->getPermissionsViaRoles()->merge($this->getDirectPermissions())->unique('name');
    }
}
