<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',                    // HINZUGEFÜGT
        'primary_role_id',         // Für das neue Rollen-System
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

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
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

    // Beziehungen
    public function primaryRole()
    {
        return $this->belongsTo(Role::class, 'primary_role_id');
    }

    public function permissions()
    {
        return $this->hasManyThrough(
            Permission::class,
            Role::class,
            'id',              // Foreign key on roles table
            'id',              // Foreign key on permissions table
            'primary_role_id', // Local key on users table
            'id'               // Local key on roles table
        )->using('role_permissions'); // Pivot table
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
        return $this->belongsToMany(ApiMonitor::class, 'user_monitor_access');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members');
    }

    // Helper Methods
    public function hasPermission($permission)
    {
        // SuperAdmin Fallback - immer zuerst prüfen
        if ($this->role === 'superadmin' || ($this->primaryRole && $this->primaryRole->name === 'superadmin')) {
            return true;
        }

        // Prüfung über Rolle mit Permissions-Tabelle
        if ($this->primaryRole && $this->primaryRole->permissions) {
            return $this->primaryRole->permissions()
                ->where('name', $permission)
                ->exists();
        }

        // Fallback für alte Rollen-Struktur
        return match($this->role) {
            'admin' => true, // Admin hat alle Rechte
            'manager' => in_array($permission, [
                'view_monitors', 'create_monitors', 'edit_monitors', 'test_monitors',
                'view_users', 'view_groups', 'view_dashboard'
            ]),
            'user' => in_array($permission, ['view_monitors', 'test_monitors', 'view_dashboard']),
            default => false
        };
    }

    public function canManageUsers()
    {
        return $this->hasPermission('manage_users') ||
            in_array($this->role, ['admin', 'superadmin']);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin' ||
            ($this->primaryRole && $this->primaryRole->name === 'superadmin');
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'superadmin']) ||
            ($this->primaryRole && in_array($this->primaryRole->name, ['admin', 'superadmin']));
    }

    public function getDisplayNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
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
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }

    public function getRoleDisplayNameAttribute()
    {
        if ($this->primaryRole) {
            return $this->primaryRole->display_name;
        }

        return match($this->role) {
            'superadmin' => 'Super Administrator',
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'user' => 'Benutzer',
            default => 'Unbekannt'
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    // Mutators
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    // Login Tracking
    public function updateLastLogin()
    {
        $this->update([
            'last_login_at' => now()
        ]);
    }

    public function markAsLoggedIn()
    {
        return $this->updateLastLogin();
    }

    // Boot method für automatische Werte
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Standard-Werte setzen
            if (!isset($user->is_active)) {
                $user->is_active = true;
            }

            if (!isset($user->email_notifications)) {
                $user->email_notifications = true;
            }

            if (!isset($user->notification_types)) {
                $user->notification_types = ['api_down', 'slow_response', 'http_error'];
            }
        });

        static::updating(function ($user) {
            // Password changed tracking
            if ($user->isDirty('password')) {
                $user->password_changed_at = now();
            }
        });
    }
}
