<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_monitor_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_monitor_id')->constrained()->onDelete('cascade');
            $table->integer('response_time_ms');
            $table->integer('status_code')->nullable();
            $table->boolean('success');
            $table->text('error_message')->nullable();
            $table->json('response_body')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_monitor_results');
    }
};
