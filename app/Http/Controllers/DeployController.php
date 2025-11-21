<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function deployNginxConfig(Request $request)
    {
        Log::info("=== API ДЕПЛОЙ ПОЛУЧЕН ===", [
            'time' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'subdomain' => $request->get('subdomain')
        ]);
        
        // Проверяем токен безопасности
        $token = $request->header('X-Deploy-Token') ?? $request->get('token');
        $expectedToken = config('app.deploy_token', 'your-secret-deploy-token-here');
        
        if ($token !== $expectedToken) {
            Log::warning("Неверный токен", ['received' => $token]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $subdomain = $request->get('subdomain');
        Log::info("Токен проверен ✓", ['subdomain' => $subdomain]);
        
        try {
            // Проверяем что конфиг существует
            $configFile = str_replace(['http://', 'https://'], '', $subdomain);
            $configPath = storage_path("nginx/{$configFile}.conf");
            
            Log::info("Проверка файла конфига", [
                'path' => $configPath,
                'exists' => file_exists($configPath),
                'size' => file_exists($configPath) ? filesize($configPath) : 0
            ]);
            
            // Список всех конфигов
            $allConfigs = glob(storage_path('nginx/*.conf'));
            Log::info("Все конфиги в storage/nginx/", [
                'count' => count($allConfigs),
                'files' => array_map('basename', $allConfigs)
            ]);
            
            // Создаём флаг-файл для cron
            $triggerFile = storage_path('framework/deploy_trigger');
            Log::info("Создание флага деплоя", ['file' => $triggerFile]);
            
            touch($triggerFile);
            
            // Ждём пока cron обработает флаг (макс 65 секунд)
            $maxWait = 65;
            $waited = 0;
            
            Log::info("Ожидание выполнения cron...");
            
            while (file_exists($triggerFile) && $waited < $maxWait) {
                sleep(1);
                $waited++;
                
                if ($waited % 10 == 0) {
                    Log::info("Ожидание: {$waited} сек...");
                }
            }
            
            // Проверяем результат
            $success = !file_exists($triggerFile); // Если файл удалён - деплой выполнен
            
            Log::info("Результат ожидания", [
                'success' => $success,
                'waited' => $waited,
                'trigger_exists' => file_exists($triggerFile)
            ]);
            
            // Читаем лог деплоя
            $deployLogFile = storage_path('logs/deploy-script.log');
            $deployLog = file_exists($deployLogFile) ? shell_exec("tail -30 {$deployLogFile}") : 'Лог не найден';
            
            $response = [
                'success' => $success,
                'deploy' => [
                    'status' => $success ? 'success' : 'error',
                    'message' => $success ? '✓ Nginx конфиг задеплоен через cron' : '⏱ Деплой в процессе (ожидание cron)',
                    'waited' => $waited,
                    'deploy_log' => $deployLog
                ]
            ];
            
            Log::info('=== ДЕПЛОЙ ЗАВЕРШЁН ===', $response);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('=== ОШИБКА ДЕПЛОЯ ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function setupSSL(Request $request)
    {
        Log::info("=== API SSL ПОЛУЧЕН ===", [
            'time' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'subdomain' => $request->get('subdomain')
        ]);
        
        // Проверяем токен безопасности
        $token = $request->header('X-Deploy-Token') ?? $request->get('token');
        $expectedToken = config('app.deploy_token', 'your-secret-deploy-token-here');
        
        if ($token !== $expectedToken) {
            Log::warning("Неверный токен", ['received' => $token]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $subdomain = $request->get('subdomain');
        Log::info("Токен проверен ✓", ['subdomain' => $subdomain]);
        
        try {
            // Создаём флаг-файл для cron с указанием поддомена
            $triggerFile = storage_path('framework/ssl_trigger');
            Log::info("Создание флага SSL", ['file' => $triggerFile, 'subdomain' => $subdomain]);
            
            file_put_contents($triggerFile, $subdomain);
            
            // Ждём пока cron обработает флаг (макс 90 секунд, т.к. SSL дольше)
            $maxWait = 90;
            $waited = 0;
            
            Log::info("Ожидание выполнения SSL cron...");
            
            while (file_exists($triggerFile) && $waited < $maxWait) {
                sleep(1);
                $waited++;
                
                if ($waited % 15 == 0) {
                    Log::info("Ожидание SSL: {$waited} сек...");
                }
            }
            
            // Проверяем результат
            $success = !file_exists($triggerFile);
            
            Log::info("Результат SSL", [
                'success' => $success,
                'waited' => $waited,
                'trigger_exists' => file_exists($triggerFile)
            ]);
            
            // Читаем лог SSL
            $sslLogFile = storage_path('logs/ssl-setup.log');
            $sslLog = file_exists($sslLogFile) ? shell_exec("tail -50 {$sslLogFile}") : 'Лог не найден';
            
            // Определяем сообщение по логу
            $message = '✓ SSL установлен успешно';
            if (strpos($sslLog, 'DNS не настроен') !== false) {
                $message = '⚠ DNS не настроен - сначала добавьте A-запись';
                $success = false;
            } elseif (strpos($sslLog, 'уже существует') !== false) {
                $message = '✓ SSL сертификат уже установлен';
            } elseif (strpos($sslLog, 'Ошибка') !== false) {
                $message = '✗ Ошибка установки SSL (см. логи)';
                $success = false;
            }
            
            $response = [
                'success' => $success,
                'message' => $message,
                'waited' => $waited,
                'ssl_log' => $sslLog
            ];
            
            Log::info('=== SSL ЗАВЕРШЁН ===', $response);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('=== ОШИБКА SSL ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

