<?php
// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use App\Models\User;
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

        // User Management Gates
        Gate::define('manage-users', function (User $user) {
            return $user->canManageUsers();
        });

        Gate::define('view-user', function (User $user, User $targetUser) {
            if ($user->isAdmin()) {
                return true;
            }

            if ($user->isManager() && !$targetUser->isAdmin()) {
                return true;
            }

            return $user->id === $targetUser->id;
        });

        Gate::define('access-monitor', function (User $user, $monitorId) {
            return $user->canAccessMonitor($monitorId);
        });

        Gate::define('manage-monitors', function (User $user) {
            return in_array($user->role, ['admin', 'manager']);
        });

        Gate::define('create-monitors', function (User $user) {
            return in_array($user->role, ['admin', 'manager']);
        });
    }
}
