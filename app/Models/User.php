<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'role',
        'is_active',
        'first_name',
        'last_name',
        'avatar',
        'department',
        'phone',
        'email_notifications',
        'notification_types',
        'monitor_access',
        'last_login_at',
        'last_login_ip',
        'password_changed_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
            'notification_types' => 'array',
            'monitor_access' => 'array',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function apiMonitors()
    {
        return $this->hasMany(ApiMonitor::class, 'created_by');
    }

    // Role Checks
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function canManageUsers()
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function canAccessMonitor($monitorId)
    {
        // Admin kann alles
        if ($this->isAdmin()) {
            return true;
        }

        // Wenn monitor_access null ist, kann User alle Monitore sehen
        if (is_null($this->monitor_access)) {
            return true;
        }

        // Prüfen ob Monitor ID in der Zugriffsliste ist
        return in_array($monitorId, $this->monitor_access ?? []);
    }

    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        // Fallback zu Gravatar
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=150";
    }

    public function getRoleDisplayAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'user' => 'Benutzer',
            default => 'Unbekannt'
        };
    }

    public function getStatusDisplayAttribute()
    {
        return $this->is_active ? 'Aktiv' : 'Deaktiviert';
    }

    public function updateLastLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    public function shouldReceiveNotification($type)
    {
        if (!$this->email_notifications || !$this->is_active) {
            return false;
        }

        if (is_null($this->notification_types)) {
            return true; // Alle Benachrichtigungen wenn nicht gesetzt
        }

        return in_array($type, $this->notification_types);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeManagers($query)
    {
        return $query->whereIn('role', ['admin', 'manager']);
    }
}
