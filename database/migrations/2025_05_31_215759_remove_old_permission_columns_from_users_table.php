<?php
// php artisan make:migration remove_old_permission_columns_from_users_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Entfernen Sie alte Permission-Spalten falls vorhanden
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'primary_role_id')) {
                $table->dropColumn('primary_role_id');
            }
            // Weitere alte Spalten hier entfernen falls vorhanden
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Falls Sie die Migration rückgängig machen möchten
            $table->string('role')->nullable();
            $table->unsignedBigInteger('primary_role_id')->nullable();
        });
    }
};
