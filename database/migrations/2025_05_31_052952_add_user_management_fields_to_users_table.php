<?php
// Migration: php artisan make:migration add_user_management_fields_to_users_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Rollen und Berechtigungen
            $table->enum('role', ['admin', 'manager', 'user'])->default('user')->after('email');
            $table->boolean('is_active')->default(true)->after('role');

            // Profil-Informationen
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('avatar')->nullable()->after('last_name');
            $table->string('department')->nullable()->after('avatar');
            $table->string('phone')->nullable()->after('department');

            // Monitoring-spezifische Einstellungen
            $table->boolean('email_notifications')->default(true)->after('phone');
            $table->json('notification_types')->nullable()->after('email_notifications'); // ['api_down', 'slow_response', 'http_error']
            $table->json('monitor_access')->nullable()->after('notification_types'); // Array of monitor IDs user can access

            // Audit-Felder
            $table->timestamp('last_login_at')->nullable()->after('monitor_access');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->timestamp('password_changed_at')->nullable()->after('last_login_ip');
            $table->unsignedBigInteger('created_by')->nullable()->after('password_changed_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            // Foreign Keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            $table->dropColumn([
                'role',
                'is_active',
                'first_name',
                'last_name',
                'avatar',
                'department',
                'phone',
                'email_notifications',
                'notification_types',
                'monitor_access',
                'last_login_at',
                'last_login_ip',
                'password_changed_at',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
