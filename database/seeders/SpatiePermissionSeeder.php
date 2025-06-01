<?php
// database/seeders/SpatiePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class SpatiePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Monitor Permissions
        $monitorPermissions = [
            'view_monitors' => 'Monitore anzeigen',
            'create_monitors' => 'Monitore erstellen',
            'edit_monitors' => 'Monitore bearbeiten',
            'delete_monitors' => 'Monitore löschen',
            'test_monitors' => 'Monitore testen',
            'export_monitors' => 'Monitor-Daten exportieren',
            'toggle_monitor_alerts' => 'Monitor-Benachrichtigungen verwalten',
        ];

        // User Management Permissions
        $userPermissions = [
            'view_users' => 'Benutzer anzeigen',
            'create_users' => 'Benutzer erstellen',
            'edit_users' => 'Benutzer bearbeiten',
            'delete_users' => 'Benutzer löschen',
            'manage_users' => 'Benutzer vollständig verwalten',
            'toggle_user_status' => 'Benutzer aktivieren/deaktivieren',
        ];

        // Group Management Permissions
        $groupPermissions = [
            'view_groups' => 'Gruppen anzeigen',
            'create_groups' => 'Gruppen erstellen',
            'edit_groups' => 'Gruppen bearbeiten',
            'delete_groups' => 'Gruppen löschen',
            'manage_groups' => 'Gruppen vollständig verwalten',
            'manage_group_members' => 'Gruppen-Mitglieder verwalten',
        ];

        // Role Management Permissions
        $rolePermissions = [
            'view_roles' => 'Rollen anzeigen',
            'create_roles' => 'Rollen erstellen',
            'edit_roles' => 'Rollen bearbeiten',
            'delete_roles' => 'Rollen löschen',
            'assign_roles' => 'Rollen zuweisen',
        ];

        // System Permissions
        $systemPermissions = [
            'view_dashboard' => 'Dashboard anzeigen',
            'view_system_logs' => 'System-Logs anzeigen',
            'manage_system_settings' => 'System-Einstellungen verwalten',
            'full_system_access' => 'Vollzugriff auf System',
        ];

        // Alle Permissions erstellen
        $allPermissions = array_merge(
            $monitorPermissions,
            $userPermissions,
            $groupPermissions,
            $rolePermissions,
            $systemPermissions
        );

        foreach ($allPermissions as $permission => $description) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
                // Optional: Description speichern (erfordert Migration)
                // 'description' => $description
            ]);
        }

        // Rollen erstellen
        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);

        $adminRole = Role::create([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);

        $moderatorRole = Role::create([
            'name' => 'Moderator',
            'guard_name' => 'web'
        ]);

        $userRole = Role::create([
            'name' => 'User',
            'guard_name' => 'web'
        ]);

        // Super Admin bekommt alle Permissions
        $superAdminRole->givePermissionTo(array_keys($allPermissions));

        // Admin bekommt alle außer System-Management
        $adminRole->givePermissionTo(array_merge(
            array_keys($monitorPermissions),
            array_keys($userPermissions),
            array_keys($groupPermissions),
            array_keys($rolePermissions),
            ['view_dashboard', 'view_system_logs']
        ));

        // Moderator bekommt Monitor- und View-Permissions
        $moderatorRole->givePermissionTo(array_merge(
            array_keys($monitorPermissions),
            ['view_users', 'view_groups', 'view_dashboard']
        ));

        // User bekommt nur View- und Test-Permissions
        $userRole->givePermissionTo([
            'view_monitors',
            'test_monitors',
            'view_dashboard'
        ]);

        // Bestehende User mit Rollen verknüpfen
        // Passen Sie die E-Mail-Adresse an
        $superAdmin = User::where('email', 'admin@test.com')->first();
        if ($superAdmin) {
            $superAdmin->assignRole($superAdminRole);
        }

        $this->command->info('Spatie Permission System successfully seeded!');
        $this->command->info('Created ' . count($allPermissions) . ' permissions and 4 roles.');
    }
}
