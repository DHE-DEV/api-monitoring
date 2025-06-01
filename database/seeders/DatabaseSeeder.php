<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User Management Seeder ausführen
        $this->call([
            UserManagementSeeder::class,
        ]);

        // Optional: Falls du zusätzliche Test-User über Factory erstellen willst
        // User::factory(10)->create();

        $this->command->info('🎉 Alle Seeder erfolgreich ausgeführt!');
    }
}
