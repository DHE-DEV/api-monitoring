<?php
// database/migrations/xxxx_add_authentication_to_api_monitors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('api_monitors', function (Blueprint $table) {
            // Authentifizierungs-Typ
            $table->enum('auth_type', [
                'none',
                'bearer_token',
                'api_key',
                'basic_auth',
                'oauth2',
                'custom_header'
            ])->default('none');

            // Verschlüsselte Authentifizierungs-Daten
            $table->text('auth_data')->nullable(); // JSON, verschlüsselt

            // Zusätzliche Auth-Konfiguration
            $table->json('auth_config')->nullable(); // Nicht-sensitive Konfiguration
        });
    }

    public function down()
    {
        Schema::table('api_monitors', function (Blueprint $table) {
            $table->dropColumn(['auth_type', 'auth_data', 'auth_config']);
        });
    }
};
