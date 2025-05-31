<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

class FixPermissionsIssue extends Command
{
    protected $signature = 'fix:permissions-issue {email?}';
    protected $description = 'Fix common permission issues';

    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            return $this->fixUserPermissions($email);
        } else {
            return $this->fixSystemPermissions();
        }
    }

    private function fixUserPermissions($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User '{$email}' not found!");
            return 1;
        }

        $this->info("🔧 Fixing permissions for: {$user->email}");

        // Prüfung 1: Hat der User eine Rolle?
        if (!$user->primary_role_id) {
            $this->warn("User has no primary_role_id, assigning user role...");
            $userRole = Role::where('name', 'user')->first();
            if ($userRole) {
                $user->update(['primary_role_id' => $userRole->id]);
                $this->info("✅ Assigned 'user' role to user");
            }
        }

        // Prüfung 2: Ist der User SuperAdmin ohne richtige Rolle?
        if ($user->role === 'superadmin' && $user->primaryRole && $user->primaryRole->name !== 'superadmin') {
            $superAdminRole = Role::where('name', 'superadmin')->first();
            if ($superAdminRole) {
                $user->update(['primary_role_id' => $superAdminRole->id]);
                $this->info("✅ Fixed SuperAdmin role assignment");
            }
        }

        // Prüfung 3: Role-Permission Verknüpfungen
        if ($user->primaryRole) {
            $rolePermissions = $user->primaryRole->permissions()->count();
            if ($rolePermissions === 0) {
                $this->warn("Role '{$user->primaryRole->name}' has no permissions!");
                $this->info("Run: php artisan db:seed --class=BasicRoleSeeder --force");
            }
        }

        return 0;
    }

    private function fixSystemPermissions()
    {
        $this->info("🔧 Fixing system permissions...");

        // 1. Basis-Berechtigungen sicherstellen
        $requiredPermissions = [
            ['name' => 'view_monitors', 'display_name' => 'Monitore anzeigen', 'category' => 'monitors'],
            ['name' => 'create_monitors', 'display_name' => 'Monitore erstellen', 'category' => 'monitors'],
            ['name' => 'edit_monitors', 'display_name' => 'Monitore bearbeiten', 'category' => 'monitors'],
            ['name' => 'delete_monitors', 'display_name' => 'Monitore löschen', 'category' => 'monitors'],
        ];

        foreach ($requiredPermissions as $permData) {
            Permission::firstOrCreate(
                ['name' => $permData['name']],
                $permData
            );
        }
        $this->info("✅ Required permissions ensured");

        // 2. SuperAdmin Rolle alle Rechte geben
        $superAdminRole = Role::where('name', 'superadmin')->first();
        if ($superAdminRole) {
            $allPermissions = Permission::all();
            $superAdminRole->permissions()->sync($allPermissions->pluck('id'));
            $this->info("✅ SuperAdmin role permissions synced ({$allPermissions->count()} permissions)");
        }

        // 3. Admin Rolle Monitor-Rechte geben
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $monitorPermissions = Permission::where('category', 'monitors')->pluck('id');
            $existingPermissions = $adminRole->permissions()->pluck('permissions.id');
            $newPermissions = $existingPermissions->merge($monitorPermissions)->unique();
            $adminRole->permissions()->sync($newPermissions);
            $this->info("✅ Admin role monitor permissions ensured");
        }

        return 0;
    }
}
