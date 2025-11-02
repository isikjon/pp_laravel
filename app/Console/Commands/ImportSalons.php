<?php

namespace App\Console\Commands;

use App\Models\Salon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportSalons extends Command
{
    protected $signature = 'salons:import {--fresh : Truncate table before import}';
    protected $description = 'Import salons from JSON files in parser_xlsx/salons_data';

    public function handle()
    {
        $jsonDir = base_path('parser_xlsx/salons_data');

        if (!is_dir($jsonDir)) {
            $this->error('Directory not found: ' . $jsonDir);
            return 1;
        }

        if ($this->option('fresh')) {
            $this->info('Truncating salons table...');
            Salon::truncate();
        }

        $files = File::glob($jsonDir . '/*.json');
        $total = count($files);
        $imported = 0;
        $errors = 0;

        $this->info("Found {$total} JSON files");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($files as $file) {
            try {
                $jsonContent = File::get($file);
                $data = json_decode($jsonContent, true);

                if (!$data || !isset($data['salon_id'])) {
                    $errors++;
                    $bar->advance();
                    continue;
                }

                Salon::updateOrCreate(
                    ['salon_id' => $data['salon_id']],
                    [
                        'salon_id' => $data['salon_id'],
                        'url' => $data['url'] ?? null,
                        'title' => $data['title'] ?? null,
                        'name' => $data['name'] ?? 'Без названия',
                        'phones' => $data['phones'] ?? [],
                        'schedule' => $data['schedule'] ?? null,
                        'city' => $data['city'] ?? 'Москва',
                        'metro' => $data['metro'] ?? null,
                        'district' => $data['district'] ?? null,
                        'coordinates' => $data['coordinates'] ?? null,
                        'map_link' => $data['map_link'] ?? null,
                        'tariffs' => $data['tariffs'] ?? [],
                        'description' => $data['description'] ?? null,
                        'images' => $data['images'] ?? [],
                        'girls' => $data['girls'] ?? [],
                        'reviews' => $data['reviews'] ?? [],
                    ]
                );

                $imported++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error('Error processing file ' . basename($file) . ': ' . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Import completed!");
        $this->info("Imported: {$imported}");
        $this->info("Errors: {$errors}");
        $this->info("Total in DB: " . Salon::count());

        return 0;
    }
}
