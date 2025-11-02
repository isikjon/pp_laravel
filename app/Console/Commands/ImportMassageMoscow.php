<?php

namespace App\Console\Commands;

use App\Models\Masseuse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportMassageMoscow extends Command
{
    protected $signature = 'massage:import-moscow {--fresh : Delete existing masseuses from Moscow}';
    protected $description = 'Import masseuses from JSON files in parser_xlsx/anketas_massage';

    public function handle()
    {
        $jsonDir = base_path('parser_xlsx/anketas_massage');

        if (!is_dir($jsonDir)) {
            $this->error('Directory not found: ' . $jsonDir);
            return 1;
        }

        if ($this->option('fresh')) {
            $this->info('Deleting masseuses from Moscow...');
            Masseuse::where('city', 'Москва')->delete();
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

                if (!$data || !isset($data['anketa_id'])) {
                    $errors++;
                    $bar->advance();
                    continue;
                }

                $girlData = $this->parseJsonData($data);
                
                Masseuse::updateOrCreate(
                    ['anketa_id' => $girlData['anketa_id']],
                    $girlData
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

        return 0;
    }

    private function parseJsonData($data)
    {
        $meetingPlaces = [];
        if (isset($data['meeting_places'])) {
            foreach ($data['meeting_places'] as $place => $value) {
                $meetingPlaces[$place] = ($value === 'да');
            }
        }

        $tariffs = [];
        if (isset($data['tariffs'])) {
            foreach ($data['tariffs'] as $tariffType => $tariffData) {
                if (is_array($tariffData)) {
                    foreach ($tariffData as $duration => $price) {
                        $key = trim(str_replace(["\n", "\t", "Тариф"], '', $tariffType)) . '_' . $duration;
                        $tariffs[$key] = $price;
                    }
                }
            }
        }

        $services = [];
        if (isset($data['services_table'])) {
            foreach ($data['services_table'] as $category => $serviceList) {
                foreach ($serviceList as $serviceName => $value) {
                    $key = $category . '_' . $serviceName;
                    $services[$key] = ($value === 'да');
                }
            }
        }

        $images = [];
        if (isset($data['media']['images']) && is_array($data['media']['images'])) {
            $images = array_filter($data['media']['images']);
        }

        $metro = '';
        if (isset($data['contact_info']['metro'])) {
            if (is_array($data['contact_info']['metro'])) {
                $metro = implode(', ', $data['contact_info']['metro']);
            } else {
                $metro = $data['contact_info']['metro'];
            }
        }

        $district = '';
        if (isset($data['contact_info']['district'])) {
            if (is_array($data['contact_info']['district'])) {
                $district = implode(', ', $data['contact_info']['district']);
            } else {
                $district = $data['contact_info']['district'];
            }
        }

        $height = null;
        if (isset($data['parameters']['height'])) {
            preg_match('/\d+/', $data['parameters']['height'], $matches);
            $height = !empty($matches) ? (int)$matches[0] : null;
        }

        $weight = null;
        if (isset($data['parameters']['weight'])) {
            preg_match('/\d+/', $data['parameters']['weight'], $matches);
            $weight = !empty($matches) ? (int)$matches[0] : null;
        }

        $bust = null;
        if (isset($data['parameters']['breast'])) {
            preg_match('/\d+/', $data['parameters']['breast'], $matches);
            $bust = !empty($matches) ? (int)$matches[0] : null;
        }

        return [
            'anketa_id' => $data['anketa_id'],
            'title' => $data['title'] ?? null,
            'name' => $data['contact_info']['name'] ?? 'Без имени',
            'age' => $data['parameters']['age'] ?? null,
            'height' => $height,
            'weight' => $weight,
            'bust' => $bust,
            'phone' => $data['contact_info']['phone'] ?? null,
            'call_availability' => $data['contact_info']['call_availability'] ?? null,
            'city' => $data['contact_info']['city'] ?? 'Москва',
            'metro' => $metro,
            'district' => $district,
            'map_link' => $data['contact_info']['map_link'] ?? null,
            'hair_color' => $data['parameters']['hair_color'] ?? null,
            'nationality' => $data['parameters']['nationality'] ?? null,
            'intimate_trim' => $data['parameters']['intimate_trim'] ?? null,
            'description' => $data['description'] ?? null,
            'meeting_places' => $meetingPlaces,
            'tariffs' => $tariffs,
            'services' => $services,
            'media_images' => $images,
            'media_video' => $data['media']['video'] ?? null,
            'original_url' => $data['url'] ?? null,
            'reviews_comments' => isset($data['reviews']['comments']) && !empty($data['reviews']['comments']) 
                ? json_encode($data['reviews']['comments']) 
                : null,
        ];
    }
}
