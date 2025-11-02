<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salons', function (Blueprint $table) {
            $table->id();
            $table->string('salon_id')->unique();
            $table->string('url')->nullable();
            $table->string('title')->nullable();
            $table->string('name');
            $table->json('phones')->nullable();
            $table->string('schedule')->nullable();
            $table->string('city')->nullable();
            $table->string('metro')->nullable();
            $table->string('district')->nullable();
            $table->string('coordinates')->nullable();
            $table->string('map_link')->nullable();
            $table->json('tariffs')->nullable();
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->json('girls')->nullable();
            $table->json('reviews')->nullable();
            $table->timestamps();
            
            $table->index('salon_id');
            $table->index('city');
            $table->index('metro');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salons');
    }
};
