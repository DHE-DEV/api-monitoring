<?php
// Migration: php artisan make:migration add_email_alerts_to_api_monitors_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('api_monitors', function (Blueprint $table) {
            $table->boolean('email_alerts_enabled')->default(true)->after('is_active');
            $table->timestamp('email_alerts_disabled_at')->nullable()->after('email_alerts_enabled');
            $table->string('email_alerts_disabled_by')->nullable()->after('email_alerts_disabled_at');
            $table->text('email_alerts_disabled_reason')->nullable()->after('email_alerts_disabled_by');
        });
    }

    public function down()
    {
        Schema::table('api_monitors', function (Blueprint $table) {
            $table->dropColumn([
                'email_alerts_enabled',
                'email_alerts_disabled_at',
                'email_alerts_disabled_by',
                'email_alerts_disabled_reason'
            ]);
        });
    }
};
