<?php
// app/Models/Group.php (KORRIGIERT)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model  // ← Das war falsch: "class Role" statt "class Group"
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
        'permissions',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'permissions' => 'array',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            if (empty($group->slug)) {
                $group->slug = Str::slug($group->name);
            }
        });

        static::updating(function ($group) {
            if ($group->isDirty('name') && empty($group->slug)) {
                $group->slug = Str::slug($group->name);
            }
        });
    }

    // Relationships
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot(['permissions', 'joined_at', 'added_by'])
            ->withTimestamps();
    }

    public function monitors()
    {
        return $this->belongsToMany(ApiMonitor::class, 'monitor_groups')
            ->withPivot(['permissions'])
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper Methods
    public function addMember(User $user, array $permissions = [], User $addedBy = null)
    {
        // Prüfen ob User bereits Mitglied ist
        if ($this->members()->where('user_id', $user->id)->exists()) {
            return $this; // Bereits Mitglied
        }

        $this->members()->attach($user->id, [
            'permissions' => $permissions, // Laravel konvertiert automatisch zu JSON
            'joined_at' => now(),
            'added_by' => $addedBy ? $addedBy->id : auth()->id() ?? 1,
        ]);

        return $this;
    }

    public function removeMember(User $user)
    {
        $this->members()->detach($user->id);
        return $this;
    }

    public function addMonitor(ApiMonitor $monitor, array $permissions = [])
    {
        // Prüfen ob Monitor bereits zugewiesen ist
        if ($this->monitors()->where('api_monitor_id', $monitor->id)->exists()) {
            return $this; // Bereits zugewiesen
        }

        $this->monitors()->attach($monitor->id, [
            'permissions' => $permissions
        ]);

        return $this;
    }

    public function removeMonitor(ApiMonitor $monitor)
    {
        $this->monitors()->detach($monitor->id);
        return $this;
    }

    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function givePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }

        return $this;
    }

    public function revokePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);

        return $this;
    }

    public function userHasPermissionInGroup(User $user, $permission)
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        if (!$member) {
            return false;
        }

        // Pivot permissions sind bereits als Array verfügbar
        $userPermissions = $member->pivot->permissions ?? [];
        return in_array($permission, $userPermissions) || $this->hasPermission($permission);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('members', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // Attributes
    public function getColorStyleAttribute()
    {
        return "background-color: {$this->color}20; color: {$this->color}; border-color: {$this->color}40;";
    }

    public static function getDefaultPermissions()
    {
        return [
            'view_monitors',
            'test_monitors',
            'edit_monitors',
            'delete_monitors',
            'manage_alerts'
        ];
    }
}
