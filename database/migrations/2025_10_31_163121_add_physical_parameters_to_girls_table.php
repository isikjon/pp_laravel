<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('girls', function (Blueprint $table) {
            $table->integer('height')->nullable()->after('age');
            $table->integer('weight')->nullable()->after('height');
            $table->integer('bust')->nullable()->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('girls', function (Blueprint $table) {
            $table->dropColumn(['height', 'weight', 'bust']);
        });
    }
};
