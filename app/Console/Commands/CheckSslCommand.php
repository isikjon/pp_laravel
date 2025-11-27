<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSslCommand extends Command
{
    protected $signature = 'ssl:check';
    
    protected $description = 'Проверяет наличие SSL на всех поддоменах';

    public function handle()
    {
        $domain = config('app.domain', 'prostitutkimoskvytake.org');
        
        try {
            $cities = DB::table('cities')->where('is_active', true)->get();
        } catch (\Exception $e) {
            $this->error("Ошибка подключения к БД: " . $e->getMessage());
            $this->info("\nИспользую список поддоменов из конфигурации...\n");
            
            $cities = collect([
                (object)['name' => 'Москва', 'subdomain' => null],
                (object)['name' => 'Санкт-Петербург', 'subdomain' => 'spb'],
                (object)['name' => 'Новгород', 'subdomain' => 'nov'],
                (object)['name' => 'Казань', 'subdomain' => 'kazan'],
            ]);
        }
        
        $this->info("Проверка SSL для всех поддоменов...\n");
        
        $results = [];
        
        foreach ($cities as $city) {
            $subdomain = $city->subdomain ? "{$city->subdomain}.{$domain}" : $domain;
            $url = "https://{$subdomain}";
            
            $this->line("Проверка: {$subdomain}");
            
            $hasSSL = $this->checkSSL($subdomain);
            
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
    
    protected function checkSSL($domain)
    {
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);
        
        $errno = 0;
        $errstr = '';
        
        $socket = @stream_socket_client(
            "ssl://{$domain}:443",
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );
        
        if ($socket === false) {
            return false;
        }
        
        $params = stream_context_get_params($socket);
        fclose($socket);
        
        return isset($params['options']['ssl']['peer_certificate']);
    }
}

