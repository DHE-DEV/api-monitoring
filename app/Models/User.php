<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 👉 automatische Laden von primaryRole
    protected $with = ['primaryRole'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'primary_role_id',
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

    public function primaryRole()
    {
        return $this->belongsTo(Role::class, 'primary_role_id');
    }

    public function hasPermission($permission)
    {
        // 1. SuperAdmin darf alles
        if ($this->role === 'superadmin' || optional($this->primaryRole)->name === 'superadmin') {
            return true;
        }

        // 2. Permission über primaryRole prüfen
        if ($this->primaryRole) {
            return $this->primaryRole->permissions()->where('name', $permission)->exists();
        }

        // 3. Fallback für alte Rollenstruktur
        return match($this->role) {
            'admin' => true,
            'manager' => in_array($permission, [
                'view_monitors', 'create_monitors', 'edit_monitors', 'test_monitors',
                'view_users', 'view_groups', 'view_dashboard'
            ]),
            'user' => in_array($permission, ['view_monitors', 'test_monitors', 'view_dashboard']),
            default => false
        };
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin' || optional($this->primaryRole)->name === 'superadmin';
    }
}
