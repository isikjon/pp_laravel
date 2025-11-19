<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncCityDataCommand extends Command
{
    protected $signature = 'city:sync {from_city} {to_city}';
    
    protected $description = 'Синхронизирует данные между городами';

    public function handle()
    {
        $fromCity = $this->argument('from_city');
        $toCity = $this->argument('to_city');
        
        $this->info("Синхронизация данных: {$fromCity} → {$toCity}");
        
        $tables = ['girls', 'masseuses'];
        
        foreach ($tables as $baseTable) {
            $fromTable = "{$baseTable}_{$fromCity}";
            $toTable = "{$baseTable}_{$toCity}";
            
            if (!Schema::hasTable($fromTable)) {
                $this->error("Таблица {$fromTable} не существует");
                continue;
            }
            
            if (!Schema::hasTable($toTable)) {
                $this->error("Таблица {$toTable} не существует");
                continue;
            }
            
            $count = DB::table($fromTable)->count();
            
            if ($count === 0) {
                $this->warn("Таблица {$fromTable} пустая, пропускаем");
                continue;
            }
            
            if ($this->confirm("Скопировать {$count} записей из {$fromTable} в {$toTable}?")) {
                DB::table($toTable)->truncate();
                
                DB::table($fromTable)->orderBy('id')->chunk(100, function($records) use ($toTable) {
                    foreach ($records as $record) {
                        $data = (array) $record;
                        unset($data['id']);
                        DB::table($toTable)->insert($data);
                    }
                });
                
                $this->info("✓ Скопировано {$count} записей в {$toTable}");
            }
        }
        
        $this->info("\n✅ Синхронизация завершена!");
        
        return 0;
    }
}

