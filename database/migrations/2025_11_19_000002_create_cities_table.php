<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('subdomain')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('girls_count')->default(0);
            $table->integer('masseuses_count')->default(0);
            $table->timestamps();
        });
        
        DB::table('cities')->insert([
            ['code' => 'moscow', 'name' => 'Москва', 'subdomain' => null, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'spb', 'name' => 'Санкт-Петербург', 'subdomain' => 'spb', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};

