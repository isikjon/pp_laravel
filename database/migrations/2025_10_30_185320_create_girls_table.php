<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('girls', function (Blueprint $table) {
            $table->id();
            $table->string('anketa_id')->unique();
            $table->string('title')->nullable();
            $table->string('name');
            $table->string('age')->nullable();
            $table->string('phone');
            $table->string('call_availability')->nullable();
            $table->string('city')->nullable();
            $table->string('metro')->nullable();
            $table->string('district')->nullable();
            $table->string('map_link')->nullable();
            $table->string('hair_color')->nullable();
            $table->string('nationality')->nullable();
            $table->string('intimate_trim')->nullable();
            $table->text('description')->nullable();
            $table->json('meeting_places')->nullable();
            $table->json('tariffs')->nullable();
            $table->json('services')->nullable();
            $table->json('media_images')->nullable();
            $table->string('media_video')->nullable();
            $table->string('original_url')->nullable();
            $table->text('reviews_comments')->nullable();
            $table->timestamps();
            
            $table->index('anketa_id');
            $table->index('metro');
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('girls');
    }
};
