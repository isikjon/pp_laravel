<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Добавляем колонку sort_order в таблицу girls
        if (Schema::hasTable('girls') && !Schema::hasColumn('girls', 'sort_order')) {
            Schema::table('girls', function (Blueprint $table) {
                $table->integer('sort_order')->default(999999)->after('anketa_id');
                $table->index(['city', 'sort_order']);
            });
        }
        
        // Добавляем колонку sort_order в таблицу masseuses
        if (Schema::hasTable('masseuses') && !Schema::hasColumn('masseuses', 'sort_order')) {
            Schema::table('masseuses', function (Blueprint $table) {
                $table->integer('sort_order')->default(999999)->after('anketa_id');
                $table->index(['city', 'sort_order']);
            });
        }
        
        // Добавляем колонку sort_order в таблицу salons
        if (Schema::hasTable('salons') && !Schema::hasColumn('salons', 'sort_order')) {
            Schema::table('salons', function (Blueprint $table) {
                $table->integer('sort_order')->default(999999)->after('salon_id');
                $table->index(['city', 'sort_order']);
            });
        }
        
        // Добавляем колонку sort_order в таблицу strip_clubs
        if (Schema::hasTable('strip_clubs') && !Schema::hasColumn('strip_clubs', 'sort_order')) {
            Schema::table('strip_clubs', function (Blueprint $table) {
                $table->integer('sort_order')->default(999999)->after('club_id');
                $table->index(['city', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('girls', 'sort_order')) {
            Schema::table('girls', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
        
        if (Schema::hasColumn('masseuses', 'sort_order')) {
            Schema::table('masseuses', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
        
        if (Schema::hasColumn('salons', 'sort_order')) {
            Schema::table('salons', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
        
        if (Schema::hasColumn('strip_clubs', 'sort_order')) {
            Schema::table('strip_clubs', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};

