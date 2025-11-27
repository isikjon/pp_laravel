<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\City;
use Illuminate\Support\Facades\Http;

class CheckSslCommand extends Command
{
    protected $signature = 'ssl:check';
    
    protected $description = 'Проверяет наличие SSL на всех поддоменах';

    public function handle()
    {
        $domain = config('app.domain', 'prostitutkimoskvytake.org');
        $cities = City::where('is_active', true)->get();
        
        $this->info("Проверка SSL для всех поддоменов...\n");
        
        $results = [];
        
        foreach ($cities as $city) {
            $subdomain = $city->subdomain ? "{$city->subdomain}.{$domain}" : $domain;
            $url = "https://{$subdomain}";
            
            $this->line("Проверка: {$subdomain}");
            
            $hasSSL = $this->checkSSL($url);
            
            $results[] = [
                'city' => $city->name,
                'subdomain' => $subdomain,
                'has_ssl' => $hasSSL,
            ];
            
            if ($hasSSL) {
                $this->info("  ✓ SSL активен");
            } else {
                $this->error("  ✗ SSL отсутствует");
            }
        }
        
        $this->newLine();
        $this->info("=== ИТОГИ ===\n");
        
        $withSSL = collect($results)->where('has_ssl', true);
        $withoutSSL = collect($results)->where('has_ssl', false);
        
        $this->info("С SSL: " . $withSSL->count());
        if ($withSSL->count() > 0) {
            foreach ($withSSL as $item) {
                $this->line("  ✓ {$item['subdomain']} ({$item['city']})");
            }
        }
        
        $this->newLine();
        $this->error("Без SSL: " . $withoutSSL->count());
        if ($withoutSSL->count() > 0) {
            foreach ($withoutSSL as $item) {
                $this->line("  ✗ {$item['subdomain']} ({$item['city']})");
            }
        }
        
        return 0;
    }
    
    protected function checkSSL($url)
    {
        try {
            $response = Http::timeout(10)->get($url);
            return true;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            if (str_contains($e->getMessage(), 'SSL') || str_contains($e->getMessage(), 'certificate')) {
                return false;
            }
            
            try {
                $httpUrl = str_replace('https://', 'http://', $url);
                Http::timeout(10)->get($httpUrl);
                return false;
            } catch (\Exception $e2) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}

