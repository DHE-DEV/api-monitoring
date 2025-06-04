<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('api_monitor_results')) {
            Schema::create('api_monitor_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('api_monitor_id')->constrained('api_monitors')->onDelete('cascade');
                $table->integer('response_time_ms')->nullable();
                $table->integer('status_code')->nullable();
                $table->boolean('is_successful')->default(false);
                $table->longText('response_body')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('checked_at')->nullable();
                $table->timestamps();

                // Indizes für Performance
                $table->index(['api_monitor_id', 'created_at']);
                $table->index(['is_successful']);
                $table->index(['checked_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('api_monitor_results');
    }
};
