<?php
// app/Console/Commands/TestSpatiePermissions.php
// php artisan make:command TestSpatiePermissions

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestSpatiePermissions extends Command
{
    protected $signature = 'test:spatie-permissions {email}';
    protected $description = 'Test Spatie permissions for a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $this->info("🔍 Testing Spatie Permissions for: {$user->email}");
        $this->info("User ID: {$user->id}");
        $this->newLine();

        // Rollen anzeigen
        $this->info("📋 Roles:");
        $roles = $user->roles;
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $this->line("  ✅ {$role->name}");
            }
        } else {
            $this->line("  ❌ No roles assigned");
        }
        $this->newLine();

        // Permissions anzeigen
        $this->info("🔑 Permissions:");
        $permissions = $user->getAllPermissions();
        if ($permissions->count() > 0) {
            foreach ($permissions as $permission) {
                $this->line("  ✅ {$permission->name}");
            }
        } else {
            $this->line("  ❌ No permissions");
        }
        $this->newLine();

        // Spezifische Permission-Tests
        $testPermissions = [
            'create_monitors',
            'view_monitors',
            'edit_monitors',
            'delete_monitors',
            'test_monitors'
        ];

        $this->info("🧪 Permission Tests:");
        foreach ($testPermissions as $permission) {
            $canDo = $user->can($permission);
            $icon = $canDo ? '✅' : '❌';
            $this->line("  {$icon} {$permission}: " . ($canDo ? 'Yes' : 'No'));
        }

        $this->newLine();
        $this->info("Test completed!");

        return 0;
    }
}
