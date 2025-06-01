<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Den alten Factory User entfernen und unseren AdminUserSeeder aufrufen
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Optional: Falls du zusätzliche Test-User über Factory erstellen willst
        // User::factory(5)->create();
    }
}
