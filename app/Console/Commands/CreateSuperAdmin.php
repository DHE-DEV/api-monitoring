<?php
// app/Console/Commands/CreateSuperAdmin.php
namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class CreateSuperAdmin extends Command
{
    protected $signature = 'user:create-superadmin {email} {--name=} {--password=secure123}';
    protected $description = 'Create a super admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->option('name') ?? 'Super Admin';
        $password = $this->option('password');

        // Prüfen ob User bereits existiert
        if (User::where('email', $email)->exists()) {
            $this->error("❌ User with email {$email} already exists!");
            return 1;
        }

        // SuperAdmin Role finden
        $superAdminRole = Role::where('name', 'superadmin')->first();
        if (!$superAdminRole) {
            $this->error("❌ SuperAdmin role not found!");
            $this->info("Run: php artisan db:seed --class=BasicRoleSeeder");
            return 1;
        }

        try {
            // User erstellen mit korrekten Feldern
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'primary_role_id' => $superAdminRole->id,
                'is_active' => true,
                'email_notifications' => true,
                'email_verified_at' => now(),
            ]);

            $this->info("✅ Super Admin created successfully!");
            $this->table(['Field', 'Value'], [
                ['Email', $user->email],
                ['Name', $user->name],
                ['Password', $password],
                ['Role', $superAdminRole->display_name],
                ['Active', $user->is_active ? 'Yes' : 'No'],
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error creating user: " . $e->getMessage());
            return 1;
        }
    }
}
