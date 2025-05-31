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
        $this->command->info('🌱 Starting database seeding...');

        // Nur RolePermissionSeeder verwenden - nicht BasicRoleSeeder
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $this->command->info('✅ Database seeding completed!');
    }
}
