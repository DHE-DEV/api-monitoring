<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_monitors', function (Blueprint $table) {
            // SoftDeletes - nur hinzufügen wenn nicht vorhanden
            if (!Schema::hasColumn('api_monitors', 'deleted_at')) {
                $table->softDeletes();
            }

            // User-Monitor Beziehung
            if (!Schema::hasColumn('api_monitors', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('email_alerts_enabled')
                    ->constrained('users')->onDelete('set null');
            }

            // Team-Zuordnung
            if (!Schema::hasColumn('api_monitors', 'team_id')) {
                $table->foreignId('team_id')->nullable()->after('created_by')
                    ->constrained('teams')->onDelete('cascade');
            }

            // Authentifizierungs-Features
            if (!Schema::hasColumn('api_monitors', 'auth_type')) {
                $table->enum('auth_type', [
                    'none', 'bearer_token', 'api_key', 'basic_auth', 'oauth2', 'custom_header'
                ])->default('none')->after('team_id');
            }

            if (!Schema::hasColumn('api_monitors', 'auth_data')) {
                $table->text('auth_data')->nullable()->after('auth_type');
            }

            if (!Schema::hasColumn('api_monitors', 'auth_config')) {
                $table->json('auth_config')->nullable()->after('auth_data');
            }

            // Erweiterte Features
            if (!Schema::hasColumn('api_monitors', 'notification_settings')) {
                $table->json('notification_settings')->nullable()->after('auth_config');
            }

            if (!Schema::hasColumn('api_monitors', 'last_checked_at')) {
                $table->timestamp('last_checked_at')->nullable()->after('notification_settings');
            }

            if (!Schema::hasColumn('api_monitors', 'consecutive_failures')) {
                $table->integer('consecutive_failures')->default(0)->after('last_checked_at');
            }

            if (!Schema::hasColumn('api_monitors', 'tags')) {
                $table->text('tags')->nullable()->after('consecutive_failures');
            }

            if (!Schema::hasColumn('api_monitors', 'uuid')) {
                $table->uuid('uuid')->nullable()->unique()->after('tags');
            }
        });

        // Indizes sicher hinzufügen
        try {
            Schema::table('api_monitors', function ($table) {
                if (Schema::hasColumn('api_monitors', 'auth_type')) {
                    $table->index(['auth_type']);
                }
                if (Schema::hasColumn('api_monitors', 'last_checked_at')) {
                    $table->index(['last_checked_at']);
                }
            });
        } catch (\Exception $e) {
            // Indizes existieren bereits
        }
    }

    public function down(): void
    {
        Schema::table('api_monitors', function (Blueprint $table) {
            // Foreign Keys entfernen
            try {
                if (Schema::hasColumn('api_monitors', 'created_by')) {
                    $table->dropForeign(['created_by']);
                }
                if (Schema::hasColumn('api_monitors', 'team_id')) {
                    $table->dropForeign(['team_id']);
                }
            } catch (\Exception $e) {}

            // Spalten entfernen (nur die existierenden)
            $columns = [];
            $columnsToCheck = [
                'deleted_at', 'created_by', 'team_id', 'auth_type',
                'auth_data', 'auth_config', 'notification_settings',
                'last_checked_at', 'consecutive_failures', 'tags', 'uuid'
            ];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('api_monitors', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
