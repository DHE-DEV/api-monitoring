<?php
namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

class DebugUserPermissions extends Command
{
    protected $signature = 'debug:permissions {email?}';
    protected $description = 'Debug user permissions system';

    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            $this->debugSpecificUser($email);
        } else {
            $this->debugSystem();
        }
    }

    private function debugSpecificUser($email)
    {
        $user = User::where('email', $email)->with(['primaryRole', 'primaryRole.permissions'])->first();

        if (!$user) {
            $this->error("User '{$email}' not found!");
            return;
        }

        $this->info("🔍 Debugging user: {$user->name} ({$user->email})");
        $this->line("User ID: {$user->id}");
        $this->line("Active: " . ($user->is_active ? 'Yes' : 'No'));
        $this->line("Legacy Role Field: " . ($user->role ?? 'null'));
        $this->line("Primary Role ID: " . ($user->primary_role_id ?? 'null'));

        if ($user->primaryRole) {
            $this->info("\n📋 Role Information:");
            $this->line("Name: {$user->primaryRole->name}");
            $this->line("Display Name: {$user->primaryRole->display_name}");
            $this->line("Level: {$user->primaryRole->level}");
            $this->line("System Role: " . ($user->primaryRole->is_system_role ? 'Yes' : 'No'));

            $permissions = $user->primaryRole->permissions;
            $this->info("\n🔑 Permissions (" . $permissions->count() . "):");

            if ($permissions->isEmpty()) {
                $this->warn("No permissions found for this role!");
            } else {
                foreach ($permissions as $permission) {
                    $this->line("  ✅ {$permission->name} ({$permission->display_name})");
                }
            }

            // Test spezifische Monitor-Berechtigungen
            $this->info("\n🧪 Monitor Permission Tests:");
            $monitorPerms = ['view_monitors', 'create_monitors', 'edit_monitors', 'delete_monitors'];
            foreach ($monitorPerms as $perm) {
                $has = $user->hasPermission($perm);
                $this->line("  {$perm}: " . ($has ? '✅ Yes' : '❌ No'));
            }

        } else {
            $this->error("No primary role assigned!");
        }
    }

    private function debugSystem()
    {
        $this->info("🔍 System Permission Debug");

        // Rollen-Übersicht
        $roles = Role::with('permissions')->get();
        $this->info("\n📋 Available Roles:");
        foreach ($roles as $role) {
            $this->line("  - {$role->name} ({$role->display_name}) - {$role->permissions->count()} permissions");
        }

        // Berechtigungen-Übersicht
        $permissions = Permission::all();
        $this->info("\n🔑 Available Permissions:");
        foreach ($permissions->groupBy('category') as $category => $perms) {
            $this->line("  {$category}:");
            foreach ($perms as $perm) {
                $this->line("    - {$perm->name} ({$perm->display_name})");
            }
        }

        // SuperAdmin Test
        $superAdmin = User::whereHas('primaryRole', function($q) {
            $q->where('name', 'superadmin');
        })->first();

        if ($superAdmin) {
            $this->info("\n👑 SuperAdmin Test:");
            $this->line("Found SuperAdmin: {$superAdmin->email}");
            $canView = $superAdmin->hasPermission('view_monitors');
            $this->line("Can view monitors: " . ($canView ? '✅ Yes' : '❌ No'));
        } else {
            $this->warn("No SuperAdmin user found!");
        }
    }
}
