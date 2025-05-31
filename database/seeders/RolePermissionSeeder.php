<?php
// database/seeders/RolePermissionSeeder.php (Vereinheitlicht)
namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding roles, permissions and groups...');

        // Create Permissions
        $this->createPermissions();

        // Create Roles
        $this->createRoles();

        // Assign Permissions to Roles
        $this->assignPermissions();

        // Create Default Groups
        $this->createGroups();

        // Update existing users
        $this->updateExistingUsers();

        $this->command->info('✅ Rollen, Berechtigungen und Gruppen wurden erfolgreich erstellt!');
    }

    private function createPermissions(): void
    {
        $permissions = [
            // Monitor Permissions
            ['name' => 'view_monitors', 'display_name' => 'Monitore anzeigen', 'category' => 'monitors', 'description' => 'Berechtigung zum Anzeigen von API-Monitoren'],
            ['name' => 'create_monitors', 'display_name' => 'Monitore erstellen', 'category' => 'monitors', 'description' => 'Berechtigung zum Erstellen neuer API-Monitore'],
            ['name' => 'edit_monitors', 'display_name' => 'Monitore bearbeiten', 'category' => 'monitors', 'description' => 'Berechtigung zum Bearbeiten von API-Monitoren'],
            ['name' => 'delete_monitors', 'display_name' => 'Monitore löschen', 'category' => 'monitors', 'description' => 'Berechtigung zum Löschen von API-Monitoren'],
            ['name' => 'test_monitors', 'display_name' => 'Monitore testen', 'category' => 'monitors', 'description' => 'Berechtigung zum manuellen Testen von API-Monitoren'],
            ['name' => 'export_monitors', 'display_name' => 'Monitor-Daten exportieren', 'category' => 'monitors', 'description' => 'Berechtigung zum Exportieren von Monitor-Daten'],
            ['name' => 'toggle_monitor_alerts', 'display_name' => 'Monitor-Benachrichtigungen verwalten', 'category' => 'monitors', 'description' => 'Berechtigung zum Aktivieren/Deaktivieren von E-Mail-Benachrichtigungen'],

            // User Management
            ['name' => 'view_users', 'display_name' => 'Benutzer anzeigen', 'category' => 'users', 'description' => 'Berechtigung zum Anzeigen von Benutzern'],
            ['name' => 'create_users', 'display_name' => 'Benutzer erstellen', 'category' => 'users', 'description' => 'Berechtigung zum Erstellen neuer Benutzer'],
            ['name' => 'edit_users', 'display_name' => 'Benutzer bearbeiten', 'category' => 'users', 'description' => 'Berechtigung zum Bearbeiten von Benutzerdaten'],
            ['name' => 'delete_users', 'display_name' => 'Benutzer löschen', 'category' => 'users', 'description' => 'Berechtigung zum Löschen von Benutzern'],
            ['name' => 'manage_users', 'display_name' => 'Benutzer vollständig verwalten', 'category' => 'users', 'description' => 'Vollzugriff auf Benutzerverwaltung'],
            ['name' => 'toggle_user_status', 'display_name' => 'Benutzer aktivieren/deaktivieren', 'category' => 'users', 'description' => 'Berechtigung zum Aktivieren/Deaktivieren von Benutzern'],

            // Group Management
            ['name' => 'view_groups', 'display_name' => 'Gruppen anzeigen', 'category' => 'groups', 'description' => 'Berechtigung zum Anzeigen von Gruppen'],
            ['name' => 'create_groups', 'display_name' => 'Gruppen erstellen', 'category' => 'groups', 'description' => 'Berechtigung zum Erstellen neuer Gruppen'],
            ['name' => 'edit_groups', 'display_name' => 'Gruppen bearbeiten', 'category' => 'groups', 'description' => 'Berechtigung zum Bearbeiten von Gruppen'],
            ['name' => 'delete_groups', 'display_name' => 'Gruppen löschen', 'category' => 'groups', 'description' => 'Berechtigung zum Löschen von Gruppen'],
            ['name' => 'manage_groups', 'display_name' => 'Gruppen vollständig verwalten', 'category' => 'groups', 'description' => 'Vollzugriff auf Gruppenverwaltung'],
            ['name' => 'manage_group_members', 'display_name' => 'Gruppen-Mitglieder verwalten', 'category' => 'groups', 'description' => 'Berechtigung zum Hinzufügen/Entfernen von Gruppen-Mitgliedern'],

            // Role Management
            ['name' => 'view_roles', 'display_name' => 'Rollen anzeigen', 'category' => 'roles', 'description' => 'Berechtigung zum Anzeigen von Rollen'],
            ['name' => 'create_roles', 'display_name' => 'Rollen erstellen', 'category' => 'roles', 'description' => 'Berechtigung zum Erstellen neuer Rollen'],
            ['name' => 'edit_roles', 'display_name' => 'Rollen bearbeiten', 'category' => 'roles', 'description' => 'Berechtigung zum Bearbeiten von Rollen'],
            ['name' => 'delete_roles', 'display_name' => 'Rollen löschen', 'category' => 'roles', 'description' => 'Berechtigung zum Löschen von Rollen'],
            ['name' => 'assign_roles', 'display_name' => 'Rollen zuweisen', 'category' => 'roles', 'description' => 'Berechtigung zum Zuweisen von Rollen an Benutzer'],

            // System Administration
            ['name' => 'view_dashboard', 'display_name' => 'Dashboard anzeigen', 'category' => 'system', 'description' => 'Berechtigung zum Anzeigen des Dashboards'],
            ['name' => 'view_system_logs', 'display_name' => 'System-Logs anzeigen', 'category' => 'system', 'description' => 'Berechtigung zum Anzeigen von System-Logs'],
            ['name' => 'manage_system_settings', 'display_name' => 'System-Einstellungen verwalten', 'category' => 'system', 'description' => 'Berechtigung zum Verwalten von System-Einstellungen'],
            ['name' => 'full_system_access', 'display_name' => 'Vollzugriff auf System', 'category' => 'system', 'description' => 'Vollzugriff auf alle System-Funktionen'],
        ];

        $this->command->info('📝 Creating permissions...');
        $created = 0;
        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
            if ($permission->wasRecentlyCreated) {
                $created++;
            }
        }
        $this->command->info("   ✅ {$created} neue Berechtigungen erstellt");
    }

    private function createRoles(): void
    {
        $this->command->info('👑 Creating roles...');

        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Administrator',
                'description' => 'Vollzugriff auf alle Funktionen des Systems',
                'level' => 100,
                'is_system_role' => true
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrator mit erweiterten Rechten für Monitore und Benutzer',
                'level' => 80,
                'is_system_role' => true
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Kann Monitore verwalten und Teams koordinieren',
                'level' => 50,
                'is_system_role' => true
            ],
            [
                'name' => 'user',
                'display_name' => 'Benutzer',
                'description' => 'Standard-Benutzer mit Grundfunktionen',
                'level' => 10,
                'is_system_role' => true
            ],
            [
                'name' => 'recadmin',
                'display_name' => 'RecAdmin',
                'description' => 'Spezielle Rolle mit Monitor-Fokus',
                'level' => 45,
                'is_system_role' => false
            ]
        ];

        $created = 0;
        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
            if ($role->wasRecentlyCreated) {
                $created++;
            }
        }
        $this->command->info("   ✅ {$created} neue Rollen erstellt");
    }

    private function assignPermissions(): void
    {
        $this->command->info('🔗 Assigning permissions to roles...');

        $allPermissions = Permission::all();

        // SuperAdmin: Alle Berechtigungen
        $superAdminRole = Role::where('name', 'superadmin')->first();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));
        $this->command->info("   ✅ SuperAdmin: {$allPermissions->count()} Berechtigungen");

        // Admin: Umfassende Berechtigungen (außer System-Vollzugriff)
        $adminRole = Role::where('name', 'admin')->first();
        $adminPermissions = Permission::whereIn('name', [
            'view_monitors', 'create_monitors', 'edit_monitors', 'delete_monitors',
            'test_monitors', 'export_monitors', 'toggle_monitor_alerts',
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'manage_users', 'toggle_user_status',
            'view_groups', 'create_groups', 'edit_groups', 'manage_group_members',
            'view_roles', 'assign_roles',
            'view_dashboard'
        ])->pluck('id');
        $adminRole->permissions()->sync($adminPermissions);
        $this->command->info("   ✅ Admin: {$adminPermissions->count()} Berechtigungen");

        // Manager: Monitor-Management und Team-Übersicht
        $managerRole = Role::where('name', 'manager')->first();
        $managerPermissions = Permission::whereIn('name', [
            'view_monitors', 'create_monitors', 'edit_monitors', 'test_monitors', 'export_monitors',
            'view_users', 'view_groups', 'view_dashboard'
        ])->pluck('id');
        $managerRole->permissions()->sync($managerPermissions);
        $this->command->info("   ✅ Manager: {$managerPermissions->count()} Berechtigungen");

        // RecAdmin: Monitor-Fokus ohne User-Management
        $recAdminRole = Role::where('name', 'recadmin')->first();
        $recAdminPermissions = Permission::whereIn('name', [
            'view_monitors', 'create_monitors', 'edit_monitors', 'delete_monitors',
            'test_monitors', 'export_monitors', 'toggle_monitor_alerts',
            'view_users', 'view_groups', 'view_dashboard'
        ])->pluck('id');
        $recAdminRole->permissions()->sync($recAdminPermissions);
        $this->command->info("   ✅ RecAdmin: {$recAdminPermissions->count()} Berechtigungen");

        // User: Nur Lese-Zugriff
        $userRole = Role::where('name', 'user')->first();
        $userPermissions = Permission::whereIn('name', [
            'view_monitors', 'test_monitors', 'view_dashboard'
        ])->pluck('id');
        $userRole->permissions()->sync($userPermissions);
        $this->command->info("   ✅ User: {$userPermissions->count()} Berechtigungen");
    }

    private function createGroups(): void
    {
        $this->command->info('👥 Creating default groups...');

        // Ersten User als Fallback für created_by finden oder erstellen
        $firstUser = User::first();
        if (!$firstUser) {
            $firstUser = User::create([
                'name' => 'System Admin',
                'email' => 'admin@system.local',
                'password' => 'temporary',
                'role' => 'superadmin',
                'primary_role_id' => Role::where('name', 'superadmin')->first()->id,
                'email_verified_at' => now(),
            ]);
        }

        $groups = [
            [
                'name' => 'Administrators',
                'slug' => 'administrators',
                'description' => 'Standard Admin-Gruppe mit erweiterten Rechten',
                'color' => '#DC2626',
                'permissions' => ['view_monitors', 'create_monitors', 'edit_monitors', 'delete_monitors', 'test_monitors'],
                'created_by' => $firstUser->id
            ],
            [
                'name' => 'Users',
                'slug' => 'users',
                'description' => 'Standard Benutzer-Gruppe',
                'color' => '#2563EB',
                'permissions' => ['view_monitors', 'test_monitors'],
                'created_by' => $firstUser->id
            ],
            [
                'name' => 'RecAdmins',
                'slug' => 'recadmins',
                'description' => 'RecAdmin Gruppe mit Monitor-Fokus',
                'color' => '#7C3AED',
                'permissions' => ['view_monitors', 'create_monitors', 'edit_monitors', 'test_monitors'],
                'created_by' => $firstUser->id
            ],
            [
                'name' => 'Managers',
                'slug' => 'managers',
                'description' => 'Manager-Gruppe für Team-Koordination',
                'color' => '#059669',
                'permissions' => ['view_monitors', 'edit_monitors', 'test_monitors'],
                'created_by' => $firstUser->id
            ]
        ];

        $created = 0;
        foreach ($groups as $groupData) {
            $group = Group::firstOrCreate(
                ['slug' => $groupData['slug']],
                $groupData
            );
            if ($group->wasRecentlyCreated) {
                $created++;
            }
        }
        $this->command->info("   ✅ {$created} neue Gruppen erstellt");
    }

    private function updateExistingUsers(): void
    {
        $this->command->info('👤 Updating existing users...');

        $userRole = Role::where('name', 'user')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $superAdminRole = Role::where('name', 'superadmin')->first();

        $usersGroup = Group::where('slug', 'users')->first();
        $adminsGroup = Group::where('slug', 'administrators')->first();

        $existingUsers = User::all();
        $updated = 0;

        foreach ($existingUsers as $user) {
            $needsUpdate = false;
            $oldRole = $user->role;

            // Primary Role zuweisen falls nicht vorhanden
            if (!$user->primary_role_id) {
                switch ($oldRole) {
                    case 'superadmin':
                        $user->primary_role_id = $superAdminRole->id;
                        break;
                    case 'admin':
                        $user->primary_role_id = $adminRole->id;
                        break;
                    case 'manager':
                        $user->primary_role_id = $adminRole->id; // Manager -> Admin
                        break;
                    case 'user':
                    default:
                        $user->primary_role_id = $userRole->id;
                        break;
                }
                $needsUpdate = true;
            }

            // Standard-Werte setzen
            if (!isset($user->is_active)) {
                $user->is_active = true;
                $needsUpdate = true;
            }

            if (!isset($user->email_notifications)) {
                $user->email_notifications = true;
                $needsUpdate = true;
            }

            if ($needsUpdate) {
                $user->save();
                $updated++;

                // Zu entsprechender Gruppe hinzufügen
                if (in_array($oldRole, ['admin', 'superadmin', 'manager'])) {
                    $adminsGroup->addMember($user);
                } else {
                    $usersGroup->addMember($user);
                }
            }
        }

        $this->command->info("   ✅ {$updated} Benutzer aktualisiert");
    }
}
