<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('api_monitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('method')->default('GET');
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->integer('interval_minutes')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_monitors');
    }
};
