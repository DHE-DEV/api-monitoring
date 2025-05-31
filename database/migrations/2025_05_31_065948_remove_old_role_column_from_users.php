<?php
// Migration: remove_old_role_column_from_users
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Erst primary_role_id hinzufügen (falls nicht vorhanden)
            if (!Schema::hasColumn('users', 'primary_role_id')) {
                $table->foreignId('primary_role_id')->nullable()->after('email')->constrained('roles')->onDelete('set null');
            }

            // Dann alte role Spalte entfernen (falls vorhanden)
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'user'])->default('user')->after('email');
            $table->dropForeign(['primary_role_id']);
            $table->dropColumn('primary_role_id');
        });
    }
};
