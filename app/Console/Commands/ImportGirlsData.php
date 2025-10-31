<?php

namespace App\Console\Commands;

use App\Models\Girl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGirlsData extends Command
{
    protected $signature = 'girls:import {--chunk=100}';
    protected $description = 'Import girls data from CSV file';

    public function handle()
    {
        $csvFile = base_path('parser_xlsx/anketas_complete.csv');
        
        if (!file_exists($csvFile)) {
            $this->error('CSV file not found: ' . $csvFile);
            return 1;
        }

        $this->info('Starting import from: ' . $csvFile);
        
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }
        
        Girl::truncate();
        
        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
        
        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle);
        
        $imported = 0;
        $batch = [];
        $chunkSize = $this->option('chunk');
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 10) continue;
            
            $data = array_combine($header, $row);
            
            $meetingPlaces = [
                'Выезд' => $data['meeting_places_Выезд'] === 'да',
                'Апартаменты' => $data['meeting_places_Апартаменты'] === 'да',
                'Квартира' => $data['meeting_places_Квартира'] === 'да',
                'Гостиница' => $data['meeting_places_Гостиница'] === 'да',
                'Баня/Сауна' => $data['meeting_places_Баня/Сауна'] === 'да',
                'Офис' => $data['meeting_places_Офис'] === 'да',
            ];
            
            $tariffs = [];
            foreach ($data as $key => $value) {
                if (strpos($key, 'tariffs_') === 0 && !empty($value) && $value !== '—') {
                    $tariffKey = str_replace('tariffs_', '', $key);
                    $tariffs[$tariffKey] = $value;
                }
            }
            
            $services = [];
            foreach ($data as $key => $value) {
                if (strpos($key, 'services_table_') === 0) {
                    $serviceKey = str_replace('services_table_', '', $key);
                    $services[$serviceKey] = $value === 'да';
                }
            }
            
            $images = [];
            if (!empty($data['media_images'])) {
                $images = array_filter(explode('; ', $data['media_images']));
            }
            for ($i = 1; $i <= 36; $i++) {
                $imgKey = "media_картинка_$i";
                if (!empty($data[$imgKey])) {
                    $images[] = $data[$imgKey];
                }
            }
            
            $batch[] = [
                'anketa_id' => $data['anketa_id'] ?? $data['catalog_info_id'] ?? null,
                'title' => $data['title'] ?? null,
                'name' => $data['catalog_info_name'] ?? $data['name'] ?? 'Без имени',
                'age' => $data['catalog_info_age'] ?? $data['parameters_age'] ?? null,
                'phone' => $data['catalog_info_phone'] ?? $data['contact_info_phone'] ?? null,
                'call_availability' => $data['contact_info_call_availability'] ?? null,
                'city' => $data['contact_info_city'] ?? null,
                'metro' => $data['catalog_info_metro'] ?? $data['contact_info_metro'] ?? null,
                'district' => $data['contact_info_district'] ?? null,
                'map_link' => $data['contact_info_map_link'] ?? null,
                'hair_color' => $data['parameters_hair_color'] ?? null,
                'nationality' => $data['parameters_nationality'] ?? null,
                'intimate_trim' => $data['parameters_intimate_trim'] ?? null,
                'description' => $data['description'] ?? null,
                'meeting_places' => json_encode($meetingPlaces),
                'tariffs' => json_encode($tariffs),
                'services' => json_encode($services),
                'media_images' => json_encode(array_values(array_unique($images))),
                'media_video' => $data['media_video'] ?? null,
                'original_url' => $data['original_url'] ?? null,
                'reviews_comments' => $data['reviews_comments'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            if (count($batch) >= $chunkSize) {
                Girl::insert($batch);
                $imported += count($batch);
                $this->info("Imported: $imported records");
                $batch = [];
            }
        }
        
        if (count($batch) > 0) {
            Girl::insert($batch);
            $imported += count($batch);
        }
        
        fclose($handle);
        
        $this->info("Import completed! Total imported: $imported records");
        return 0;
    }
}
