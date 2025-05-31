<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Prüfen und hinzufügen, falls nicht vorhanden
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('password');
            }

            if (!Schema::hasColumn('users', 'primary_role_id')) {
                $table->foreignId('primary_role_id')->nullable()->after('role')->constrained('roles')->onDelete('set null');
            }

            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('last_name');
            }

            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('department');
            }

            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('avatar');
            }

            if (!Schema::hasColumn('users', 'email_notifications')) {
                $table->boolean('email_notifications')->default(true)->after('is_active');
            }

            if (!Schema::hasColumn('users', 'notification_types')) {
                $table->json('notification_types')->nullable()->after('email_notifications');
            }

            if (!Schema::hasColumn('users', 'monitor_access')) {
                $table->json('monitor_access')->nullable()->after('notification_types');
            }

            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('monitor_access');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('password_changed_at');
            }

            if (!Schema::hasColumn('users', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('last_login_at')->constrained('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('users', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'role', 'primary_role_id', 'first_name', 'last_name',
                'department', 'phone', 'avatar', 'is_active',
                'email_notifications', 'notification_types', 'monitor_access',
                'password_changed_at', 'last_login_at', 'created_by', 'updated_by'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
