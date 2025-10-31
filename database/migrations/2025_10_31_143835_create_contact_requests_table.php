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
        Schema::create('contact_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('girl_anketa_id')->nullable();
            $table->string('girl_name')->nullable();
            $table->string('page_url')->nullable();
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new');
            $table->timestamps();
            
            $table->index('status');
            $table->index('girl_anketa_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_requests');
    }
};
