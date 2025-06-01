<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions definieren
        $permissions = [
            'view-monitors',
            'manage-monitors',
            'view-users',
            'manage-users',
            'view-settings',
            'manage-settings',
            'export-data',
            'admin-access',
        ];

        // Permissions erstellen (falls Spatie Permission installiert ist)
        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            foreach ($permissions as $permission) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web'
                ]);
            }
        }

        // Roles erstellen und Permissions zuweisen
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            // Admin Role - alle Rechte
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => 'admin',
                'guard_name' => 'web'
            ]);
            $adminRole->syncPermissions($permissions);

            // Manager Role - kann Monitore und User verwalten, aber keine System-Settings
            $managerRole = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => 'manager',
                'guard_name' => 'web'
            ]);
            $managerRole->syncPermissions([
                'view-monitors',
                'manage-monitors',
                'view-users',
                'manage-users',
                'export-data',
            ]);

            // Viewer Role - kann nur anschauen
            $viewerRole = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => 'viewer',
                'guard_name' => 'web'
            ]);
            $viewerRole->syncPermissions([
                'view-monitors',
                'view-users',
            ]);
        }

        // Admin User erstellen
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@apimonitor.local'],
            [
                'name' => 'API Monitor Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Manager User erstellen
        $managerUser = User::updateOrCreate(
            ['email' => 'manager@apimonitor.local'],
            [
                'name' => 'API Monitor Manager',
                'password' => Hash::make('manager123'),
                'email_verified_at' => now(),
            ]
        );

        // Viewer User erstellen
        $viewerUser = User::updateOrCreate(
            ['email' => 'viewer@apimonitor.local'],
            [
                'name' => 'API Monitor Viewer',
                'password' => Hash::make('viewer123'),
                'email_verified_at' => now(),
            ]
        );

        // Roles zuweisen (falls Spatie Permission verfügbar)
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $adminUser->assignRole('admin');
            $managerUser->assignRole('manager');
            $viewerUser->assignRole('viewer');

            $this->command->info('✅ Admin User erstellt: admin@apimonitor.local / admin123 (Admin Role)');
            $this->command->info('✅ Manager User erstellt: manager@apimonitor.local / manager123 (Manager Role)');
            $this->command->info('✅ Viewer User erstellt: viewer@apimonitor.local / viewer123 (Viewer Role)');
            $this->command->info('');
            $this->command->info('🔐 Permissions erstellt:');
            $this->command->info('   Admin: Alle Rechte');
            $this->command->info('   Manager: Monitore + User verwalten, Export');
            $this->command->info('   Viewer: Nur ansehen');
        } else {
            $this->command->info('✅ Admin User erstellt: admin@apimonitor.local / admin123');
            $this->command->info('✅ Manager User erstellt: manager@apimonitor.local / manager123');
            $this->command->info('✅ Viewer User erstellt: viewer@apimonitor.local / viewer123');
            $this->command->info('ℹ️  Spatie Permission nicht installiert - keine Roles zugewiesen');
        }

        $this->command->info('');
        $this->command->info('🚀 User Management Setup abgeschlossen!');
    }
}
