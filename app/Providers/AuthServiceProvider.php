<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // keine Policies für ApiMonitor setzen!
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Globale Vorabprüfung für SuperAdmin
        Gate::before(function (User $user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // GATE für Monitore
        Gate::define('view-monitors', function (User $user) {
            $hasPermission = $user->hasPermission('view_monitors');

            \Log::debug('GATE DEBUG: view-monitors', [
                'user_id' => $user->id,
                'role' => $user->role,
                'primary_role' => optional($user->primaryRole)->name,
                'has_permission' => $hasPermission,
            ]);

            return $hasPermission;
        });

        Gate::define('test-monitors', function (User $user) {
            return $user->hasPermission('test_monitors');
        });

        Gate::define('manage-users', function (User $user) {
            return $user->hasPermission('manage_users');
        });
    }
}
