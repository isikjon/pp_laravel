<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListCitiesCommand extends Command
{
    protected $signature = 'city:list';
    
    protected $description = 'Показывает список всех городов';

    public function handle()
    {
        $this->info("Список городов в системе:\n");
        
        $settings = DB::table('home_page_settings')->get();
        
        $cities = [];
        
        foreach ($settings as $setting) {
            $cityCode = $setting->city;
            
            $girlsTable = "girls_{$cityCode}";
            $masseusesTable = "masseuses_{$cityCode}";
            
            $girlsCount = Schema::hasTable($girlsTable) ? DB::table($girlsTable)->count() : 0;
            $masseusesCount = Schema::hasTable($masseusesTable) ? DB::table($masseusesTable)->count() : 0;
            
            $cities[] = [
                'Код' => $cityCode,
                'Название' => $cityCode === 'spb' ? 'Санкт-Петербург' : 'Москва',
                'Девушки' => $girlsCount,
                'Массажистки' => $masseusesCount,
                'SEO Title' => $setting->title ?? '-',
            ];
        }
        
        $this->table(
            ['Код', 'Название', 'Девушки', 'Массажистки', 'SEO Title'],
            $cities
        );
        
        $allTables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name LIKE '%_moscow' OR name LIKE '%_spb'");
        
        if (count($allTables) > 0) {
            $this->info("\nТаблицы в БД:");
            foreach ($allTables as $table) {
                $count = DB::table($table->name)->count();
                $this->line("  • {$table->name} ({$count} записей)");
            }
        }
        
        return 0;
    }
}

