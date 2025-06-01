<?php
// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Super Admin hat immer alle Rechte
        Gate::before(function ($user, $ability) {
            if ($user && $user->isSuperAdmin()) {
                return true;
            }
            return null; // Lass Spatie weiter prüfen
        });

        // Optional: Explizite Gates für bessere Performance
        // (Spatie kann diese automatisch, aber explizite Gates sind schneller)

        Gate::define('create_monitors', function ($user) {
            return $user->hasPermissionTo('create_monitors');
        });

        Gate::define('view_monitors', function ($user) {
            return $user->hasPermissionTo('view_monitors');
        });

        Gate::define('edit_monitors', function ($user) {
            return $user->hasPermissionTo('edit_monitors');
        });

        Gate::define('delete_monitors', function ($user) {
            return $user->hasPermissionTo('delete_monitors');
        });

        Gate::define('test_monitors', function ($user) {
            return $user->hasPermissionTo('test_monitors');
        });

        Gate::define('toggle_monitor_alerts', function ($user) {
            return $user->hasPermissionTo('toggle_monitor_alerts');
        });

        Gate::define('export_monitors', function ($user) {
            return $user->hasPermissionTo('export_monitors');
        });

        // User Management
        Gate::define('view_users', function ($user) {
            return $user->hasPermissionTo('view_users');
        });

        Gate::define('create_users', function ($user) {
            return $user->hasPermissionTo('create_users');
        });

        Gate::define('edit_users', function ($user) {
            return $user->hasPermissionTo('edit_users');
        });

        Gate::define('delete_users', function ($user) {
            return $user->hasPermissionTo('delete_users');
        });

        Gate::define('manage_users', function ($user) {
            return $user->hasPermissionTo('manage_users');
        });

        // System Management
        Gate::define('view_dashboard', function ($user) {
            return $user->hasPermissionTo('view_dashboard');
        });

        Gate::define('manage_system_settings', function ($user) {
            return $user->hasPermissionTo('manage_system_settings');
        });

        Gate::define('view_system_logs', function ($user) {
            return $user->hasPermissionTo('view_system_logs');
        });
    }
}
