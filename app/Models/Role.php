<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'level',
        'is_system_role',
        'is_active'
    ];

    protected $casts = [
        'is_system_role' => 'boolean',
        'is_active' => 'boolean',
        'level' => 'integer'
    ];

    // Relationships
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
            ->withTimestamps();
    }

    public function primaryUsers()
    {
        return $this->hasMany(User::class, 'primary_role_id');
    }

    // Helper Methods
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('name', $permission)->exists();
        }

        return $this->permissions()->where('id', $permission->id)->exists();
    }

    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching([$permission->id]);
        return $this;
    }

    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission->id);
        return $this;
    }

    // Scopes
    public function scopeSystemRoles($query)
    {
        return $query->where('is_system_role', true);
    }

    public function scopeCustomRoles($query)
    {
        return $query->where('is_system_role', false);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', '>=', $level);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Role Hierarchy Checks
    public function isSuperAdmin()
    {
        return $this->level >= 100;
    }

    public function isAdmin()
    {
        return $this->level >= 50;
    }

    public function isUser()
    {
        return $this->level >= 10;
    }

    public function canManageRole(Role $role)
    {
        return $this->level > $role->level;
    }

    public function canAssignRole(Role $role)
    {
        return $this->level > $role->level;
    }
}
