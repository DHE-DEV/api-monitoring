<?php
// Migration 3: add_group_permissions_to_monitors
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Monitor Group Assignments
        Schema::create('monitor_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_monitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->json('permissions')->nullable(); // view, edit, delete, test
            $table->timestamps();

            $table->unique(['api_monitor_id', 'group_id']);
        });

        // Update api_monitors table
        Schema::table('api_monitors', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'groups_only', 'superadmin_only'])->default('groups_only')->after('is_active');
            $table->json('access_permissions')->nullable()->after('visibility'); // Default permissions für neue Gruppen
        });

        // Update users table für erweiterte Rollen
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role'); // Entfernen da wir jetzt role_id verwenden
            $table->foreignId('primary_role_id')->nullable()->after('email')->constrained('roles')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['primary_role_id']);
            $table->dropColumn('primary_role_id');
            $table->enum('role', ['admin', 'manager', 'user'])->default('user')->after('email');
        });

        Schema::table('api_monitors', function (Blueprint $table) {
            $table->dropColumn(['visibility', 'access_permissions']);
        });

        Schema::dropIfExists('monitor_groups');
    }
};
