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
            
            // ЖДЁМ 60 СЕКУНД
            Log::info("⏱️ ЗАДЕРЖКА 60 СЕКУНД НАЧАТА", ['start' => now()->toDateTimeString()]);
            
            for ($i = 1; $i <= 60; $i++) {
                sleep(1);
                if ($i % 10 == 0) {
                    Log::info("⏱️ Прошло {$i} секунд...");
                }
            }
            
            Log::info("⏱️ ЗАДЕРЖКА ЗАВЕРШЕНА", ['end' => now()->toDateTimeString()]);
            
            // Деплой nginx конфига через wrapper
            Log::info("Запуск wrapper...");
            exec('/usr/bin/sudo /usr/local/bin/deploy-nginx-wrapper 2>&1', $wrapperOutput, $wrapperReturn);
            
            Log::info("Wrapper выполнен", [
                'exit_code' => $wrapperReturn,
                'output' => $wrapperOutput
            ]);
            
            // Читаем последние строки лога деплоя
            $deployLogFile = storage_path('logs/deploy.log');
            $deployLog = file_exists($deployLogFile) ? shell_exec("tail -20 {$deployLogFile}") : 'Лог не найден';
            
            Log::info("Лог деплоя", ['log' => $deployLog]);
            
            // Проверяем успешность по exit code wrapper'а
            $success = $wrapperReturn === 0;
            
            $response = [
                'success' => $success,
                'deploy' => [
                    'status' => $success ? 'success' : 'error',
                    'message' => $success ? '✓ Nginx конфиг задеплоен и перезагружен' : '✗ Ошибка деплоя',
                    'wrapper_output' => implode("\n", $wrapperOutput ?? []),
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
}

