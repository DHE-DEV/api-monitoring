<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Cache leeren für Permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Berechtigungen erstellen
        $permissions = [
            // Dashboard
            'view-dashboard',

            // Monitor Management
            'view-monitors',
            'create-monitors',
            'edit-monitors',
            'delete-monitors',
            'test-monitors',

            // Monitor Results
            'view-results',
            'export-results',

            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Role Management
            'view-roles',
            'manage-roles',

            // System Settings
            'view-settings',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Rollen erstellen
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $monitorRole = Role::firstOrCreate(['name' => 'Monitor Operator']);
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer']);

        // Super Admin - alle Berechtigungen
        $superAdminRole->givePermissionTo(Permission::all());

        // Manager - fast alle Berechtigungen (außer User-Löschung und Rollen-Management)
        $managerRole->givePermissionTo([
            'view-dashboard',
            'view-monitors', 'create-monitors', 'edit-monitors', 'delete-monitors', 'test-monitors',
            'view-results', 'export-results',
            'view-users', 'create-users', 'edit-users',
            'view-settings'
        ]);

        // Monitor Operator - nur Monitor-bezogene Aktionen
        $monitorRole->givePermissionTo([
            'view-dashboard',
            'view-monitors', 'create-monitors', 'edit-monitors', 'test-monitors',
            'view-results', 'export-results'
        ]);

        // Viewer - nur Ansichts-Rechte
        $viewerRole->givePermissionTo([
            'view-dashboard',
            'view-monitors',
            'view-results'
        ]);

        // Standard Admin User erstellen (nur wenn noch nicht vorhanden)
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@apimonitor.local'],
            [
                'name' => 'System Administrator',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        if (!$adminUser->hasRole('Super Admin')) {
            $adminUser->assignRole('Super Admin');
        }

        // Demo Manager User
        $managerUser = User::firstOrCreate(
            ['email' => 'manager@apimonitor.local'],
            [
                'name' => 'Demo Manager',
                'first_name' => 'Demo',
                'last_name' => 'Manager',
                'password' => Hash::make('manager123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        if (!$managerUser->hasRole('Manager')) {
            $managerUser->assignRole('Manager');
        }

        // Demo Viewer User
        $viewerUser = User::firstOrCreate(
            ['email' => 'viewer@apimonitor.local'],
            [
                'name' => 'Demo Viewer',
                'first_name' => 'Demo',
                'last_name' => 'Viewer',
                'password' => Hash::make('viewer123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        if (!$viewerUser->hasRole('Viewer')) {
            $viewerUser->assignRole('Viewer');
        }

        $this->command->info('Rollen und Berechtigungen erfolgreich erstellt!');
        $this->command->info('Demo-Benutzer:');
        $this->command->info('Admin: admin@apimonitor.local / admin123');
        $this->command->info('Manager: manager@apimonitor.local / manager123');
        $this->command->info('Viewer: viewer@apimonitor.local / viewer123');
    }
}
