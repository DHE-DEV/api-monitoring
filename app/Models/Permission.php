<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withTimestamps();
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public static function getCategories()
    {
        return [
            'monitors' => 'API Monitoring',
            'users' => 'Benutzerverwaltung',
            'groups' => 'Gruppenverwaltung',
            'roles' => 'Rollenverwaltung',
            'system' => 'System-Administration'
        ];
    }

    public function getCategoryDisplayAttribute()
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? $this->category;
    }

    public static function getAllPermissions()
    {
        return self::with('roles')->get()->groupBy('category');
    }

    public static function getPermissionsByCategory($category)
    {
        return self::where('category', $category)->get();
    }
}
