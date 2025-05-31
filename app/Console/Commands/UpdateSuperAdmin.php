<?php
// app/Console/Commands/UpdateSuperAdmin.php
namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class UpdateSuperAdmin extends Command
{
    protected $signature = 'user:make-superadmin {email}';
    protected $description = 'Make a user a super admin';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $superAdminRole = Role::where('name', 'superadmin')->first();

        if (!$superAdminRole) {
            $this->error("Super Admin role not found! Run RolePermissionSeeder first.");
            return 1;
        }

        $user->update(['primary_role_id' => $superAdminRole->id]);

        $this->info("User {$user->name} ({$user->email}) is now a Super Admin!");
        return 0;
    }
}
