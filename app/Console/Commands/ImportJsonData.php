<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportJsonData extends Command
{
    protected $signature = 'data:import-json {--fresh : Truncate tables before import}';
    protected $description = 'Import data from JSON files to new separated tables';

    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Truncating tables...');
            DB::table('girls_moscow')->truncate();
            DB::table('girls_spb')->truncate();
            DB::table('masseuses_moscow')->truncate();
            DB::table('masseuses_spb')->truncate();
        }

        $this->importGirlsMoscow();
        $this->importGirlsSpb();
        $this->importMasseusesMoscow();
        $this->importMasseusesSpb();

        $this->newLine(2);
        $this->info('Import completed!');
        $this->info('Girls Moscow: ' . DB::table('girls_moscow')->count());
        $this->info('Girls SPB: ' . DB::table('girls_spb')->count());
        $this->info('Masseuses Moscow: ' . DB::table('masseuses_moscow')->count());
        $this->info('Masseuses SPB: ' . DB::table('masseuses_spb')->count());

        return 0;
    }

    private function importGirlsMoscow()
    {
        $jsonDir = base_path('parser_xlsx/anketas');
        $this->info('Importing girls_moscow from ' . $jsonDir);

        if (!is_dir($jsonDir)) {
            $this->error('Directory not found: ' . $jsonDir);
            return;
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

                $girlData = $this->parseGirlJsonData($data);
                
                DB::table('girls_moscow')->updateOrInsert(
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
        $this->newLine();
        $this->info("Girls Moscow: Imported {$imported}, Errors {$errors}");
    }

    private function importGirlsSpb()
    {
        $jsonDir = base_path('parser_xlsx/anketas_spb');
        $this->info('Importing girls_spb from ' . $jsonDir);

        if (!is_dir($jsonDir)) {
            $this->error('Directory not found: ' . $jsonDir);
            return;
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

                if (!$data || !isset($data['anketa_id']) || !isset($data['catalog_info']['id'])) {
                    $errors++;
                    $bar->advance();
                    continue;
                }

                $girlData = $this->parseGirlJsonData($data);
                
                DB::table('girls_spb')->updateOrInsert(
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
        $this->newLine();
        $this->info("Girls SPB: Imported {$imported}, Errors {$errors}");
    }

    private function importMasseusesMoscow()
    {
        $jsonDir = base_path('parser_xlsx/anketas_massage');
        $this->info('Importing masseuses_moscow from ' . $jsonDir);

        if (!is_dir($jsonDir)) {
            $this->error('Directory not found: ' . $jsonDir);
            return;
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

                $masseuseData = $this->parseMasseuseJsonData($data);
                
                DB::table('masseuses_moscow')->updateOrInsert(
                    ['anketa_id' => $masseuseData['anketa_id']],
                    $masseuseData
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
        $this->newLine();
        $this->info("Masseuses Moscow: Imported {$imported}, Errors {$errors}");
    }

    private function importMasseusesSpb()
    {
        $jsonDir = base_path('parser_xlsx/anketas_massage-spb');
        $this->info('Importing masseuses_spb from ' . $jsonDir);

        if (!is_dir($jsonDir)) {
            $this->error('Directory not found: ' . $jsonDir);
            return;
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

                $masseuseData = $this->parseMasseuseJsonData($data);
                
                DB::table('masseuses_spb')->updateOrInsert(
                    ['anketa_id' => $masseuseData['anketa_id']],
                    $masseuseData
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
        $this->newLine();
        $this->info("Masseuses SPB: Imported {$imported}, Errors {$errors}");
    }

    private function parseGirlJsonData($data)
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
        } elseif (isset($data['catalog_info']['metro'])) {
            $metro = str_replace('м. ', '', $data['catalog_info']['metro']);
        }

        $district = '';
        if (isset($data['contact_info']['district'])) {
            if (is_array($data['contact_info']['district'])) {
                $district = implode(', ', $data['contact_info']['district']);
            } else {
                $district = $data['contact_info']['district'];
            }
        }

        $age = $data['catalog_info']['age'] ?? $data['parameters']['age'] ?? null;
        if ($age && preg_match('/\d+/', $age, $matches)) {
            $age = $matches[0] . ' лет';
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
            'anketa_id' => $data['anketa_id'] ?? $data['catalog_info']['id'] ?? null,
            'sort_order' => 999999,
            'title' => $data['title'] ?? null,
            'name' => $data['catalog_info']['name'] ?? $data['contact_info']['name'] ?? 'Без имени',
            'age' => $age,
            'height' => $height,
            'weight' => $weight,
            'bust' => $bust,
            'phone' => $data['catalog_info']['phone'] ?? $data['contact_info']['phone'] ?? null,
            'call_availability' => $data['contact_info']['call_availability'] ?? null,
            'city' => $data['contact_info']['city'] ?? 'Москва',
            'metro' => $metro,
            'district' => $district,
            'map_link' => $data['contact_info']['map_link'] ?? null,
            'hair_color' => $data['parameters']['hair_color'] ?? null,
            'nationality' => $data['parameters']['nationality'] ?? null,
            'intimate_trim' => $data['parameters']['intimate_trim'] ?? null,
            'description' => $data['description'] ?? null,
            'meeting_places' => json_encode($meetingPlaces),
            'tariffs' => json_encode($tariffs),
            'services' => json_encode($services),
            'media_images' => json_encode($images),
            'media_video' => $data['media']['video'] ?? null,
            'original_url' => $data['original_url'] ?? $data['catalog_info']['url'] ?? $data['url'] ?? null,
            'reviews_comments' => isset($data['reviews']['comments']) && !empty($data['reviews']['comments']) 
                ? json_encode($data['reviews']['comments']) 
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function parseMasseuseJsonData($data)
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
            'sort_order' => 999999,
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
            'meeting_places' => json_encode($meetingPlaces),
            'tariffs' => json_encode($tariffs),
            'services' => json_encode($services),
            'media_images' => json_encode($images),
            'media_video' => $data['media']['video'] ?? null,
            'original_url' => $data['url'] ?? null,
            'reviews_comments' => isset($data['reviews']['comments']) && !empty($data['reviews']['comments']) 
                ? json_encode($data['reviews']['comments']) 
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
