<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User erstellen (oder aktualisieren falls vorhanden)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@apimonitor.local'],
            [
                'name' => 'API Monitor Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Falls Spatie Permission installiert ist, Admin Role zuweisen
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            // Admin Role erstellen falls nicht vorhanden
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => 'admin',
                'guard_name' => 'web'
            ]);

            // Permissions erstellen
            $permissions = [
                'view-monitors',
                'manage-monitors',
                'view-users',
                'manage-users',
                'view-settings',
                'manage-settings',
                'export-data',
            ];

            foreach ($permissions as $permission) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web'
                ]);
            }

            // Alle Permissions der Admin Role zuweisen
            $adminRole->syncPermissions($permissions);

            // User die Admin Role zuweisen
            $adminUser->assignRole('admin');

            $this->command->info('✅ Admin User erstellt: admin@apimonitor.local / admin123');
            $this->command->info('✅ Admin Role mit allen Permissions zugewiesen');
        } else {
            $this->command->info('✅ Admin User erstellt: admin@apimonitor.local / admin123');
            $this->command->info('ℹ️  Spatie Permission nicht installiert - keine Roles zugewiesen');
        }

        // Zusätzlicher Test-User (optional)
        $testUser = User::updateOrCreate(
            ['email' => 'test@apimonitor.local'],
            [
                'name' => 'Test User',
                'password' => Hash::make('test123'),
                'email_verified_at' => now(),
            ]
        );

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            // Viewer Role für Test User
            $viewerRole = \Spatie\Permission\Models\Role::firstOrCreate([
                'name' => 'viewer',
                'guard_name' => 'web'
            ]);

            $viewerRole->syncPermissions(['view-monitors']);
            $testUser->assignRole('viewer');

            $this->command->info('✅ Test User erstellt: test@apimonitor.local / test123 (Viewer Role)');
        } else {
            $this->command->info('✅ Test User erstellt: test@apimonitor.local / test123');
        }
    }
}
