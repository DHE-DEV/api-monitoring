<?php
// app/Console/Commands/CreateAdminUser.php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    protected $signature = 'user:create-admin {email} {--name=} {--password=}';
    protected $description = 'Create an admin user';

    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->option('name') ?? 'Admin User';
        $password = $this->option('password') ?? Str::random(12);

        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
            'email_notifications' => true,
            'password_changed_at' => now(),
        ]);

        $this->info("Admin user created successfully!");
        $this->table(['Field', 'Value'], [
            ['Email', $user->email],
            ['Name', $user->name],
            ['Password', $password],
            ['Role', $user->role],
        ]);

        return 0;
    }
}
