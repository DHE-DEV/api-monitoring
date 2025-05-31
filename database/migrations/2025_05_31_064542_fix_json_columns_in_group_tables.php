<?php
// Migration: fix_json_columns_in_group_tables
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('group_members', function (Blueprint $table) {
            $table->json('permissions')->nullable()->change();
        });

        Schema::table('monitor_groups', function (Blueprint $table) {
            $table->json('permissions')->nullable()->change();
        });
    }

    public function down()
    {
        // Rollback falls nötig
        Schema::table('group_members', function (Blueprint $table) {
            $table->text('permissions')->nullable()->change();
        });

        Schema::table('monitor_groups', function (Blueprint $table) {
            $table->text('permissions')->nullable()->change();
        });
    }
};
