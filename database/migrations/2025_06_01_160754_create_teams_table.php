<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Teams Tabelle überspringen (existiert bereits)

        // Nur fehlende User-Spalten hinzufügen
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('team_id');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }

            if (!Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('users', 'is_active')) $columns[] = 'is_active';
            if (Schema::hasColumn('users', 'last_login_at')) $columns[] = 'last_login_at';
            if (Schema::hasColumn('users', 'preferences')) $columns[] = 'preferences';

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
