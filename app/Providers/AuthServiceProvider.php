<?php
// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use App\Models\User;
use App\Models\ApiMonitor;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        // Dynamic Permission Gates
        Gate::before(function (User $user, $ability) {
            // Superadmin kann alles
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Monitor Gates
        Gate::define('view-monitors', function (User $user) {
            return $user->hasPermission('view_monitors');
        });

        Gate::define('create-monitors', function (User $user) {
            return $user->hasPermission('create_monitors');
        });

        Gate::define('edit-monitors', function (User $user) {
            return $user->hasPermission('edit_monitors');
        });

        Gate::define('delete-monitors', function (User $user) {
            return $user->hasPermission('delete_monitors');
        });

        Gate::define('test-monitors', function (User $user) {
            return $user->hasPermission('test_monitors');
        });

        Gate::define('manage-specific-monitor', function (User $user, ApiMonitor $monitor) {
            return $user->canManageMonitor($monitor);
        });

        // User Management Gates
        Gate::define('manage-users', function (User $user) {
            return $user->hasPermission('manage_user_roles') || $user->hasPermission('create_users');
        });

        Gate::define('view-user', function (User $user, User $targetUser) {
            if ($user->isSuperAdmin()) {
                return true;
            }

            if ($user->isAdmin() && !$targetUser->isSuperAdmin()) {
                return true;
            }

            return $user->id === $targetUser->id;
        });

        // Group Management Gates
        Gate::define('manage-groups', function (User $user) {
            return $user->hasPermission('create_groups') || $user->hasPermission('edit_groups');
        });

        Gate::define('view-groups', function (User $user) {
            return $user->hasPermission('view_groups');
        });

        // Role Management Gates
        Gate::define('manage-roles', function (User $user) {
            return $user->hasPermission('create_roles') || $user->hasPermission('edit_roles');
        });

        Gate::define('assign-roles', function (User $user) {
            return $user->hasPermission('assign_roles');
        });

        Gate::define('manage-role', function (User $user, $targetRole) {
            // Nur Rollen mit niedrigerem Level verwalten
            if ($user->primaryRole) {
                return $user->primaryRole->canManageRole($targetRole);
            }
            return false;
        });
    }
}
