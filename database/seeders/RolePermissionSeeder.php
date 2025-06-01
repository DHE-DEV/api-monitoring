// database/seeders/RolePermissionSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Berechtigungen erstellen
        $permissions = [
            'view-dashboard',
            'manage-monitors',
            'create-monitors',
            'edit-monitors',
            'delete-monitors',
            'view-results',
            'export-data',
            'manage-users',
            'manage-roles',
            'view-settings',
            'manage-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Rollen erstellen
        $adminRole = Role::create(['name' => 'Super Admin']);
        $managerRole = Role::create(['name' => 'Manager']);
        $viewerRole = Role::create(['name' => 'Viewer']);

        // Berechtigungen zuweisen
        $adminRole->givePermissionTo(Permission::all());

        $managerRole->givePermissionTo([
            'view-dashboard',
            'manage-monitors',
            'create-monitors',
            'edit-monitors',
            'view-results',
            'export-data'
        ]);

        $viewerRole->givePermissionTo([
            'view-dashboard',
            'view-results'
        ]);

        // Standard Admin User erstellen
        $adminUser = User::create([
            'name' => 'Administrator',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $adminUser->assignRole('Super Admin');
    }
}
