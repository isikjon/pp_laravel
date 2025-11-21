<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    public function deployNginxConfig(Request $request)
    {
        // Проверяем токен безопасности
        $token = $request->header('X-Deploy-Token') ?? $request->get('token');
        $expectedToken = config('app.deploy_token', 'your-secret-deploy-token-here');
        
        if ($token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $subdomain = $request->get('subdomain');
        
        try {
            // Деплой nginx конфига через wrapper (без sudo пароля)
            exec('/usr/bin/sudo /usr/local/bin/deploy-nginx-wrapper 2>&1', $wrapperOutput, $wrapperReturn);
            
            // Читаем последние строки лога деплоя для подтверждения
            $deployLogFile = storage_path('logs/deploy.log');
            $deployLog = file_exists($deployLogFile) ? shell_exec("tail -10 {$deployLogFile}") : 'Лог не найден';
            
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
            
            // Настройка SSL отключена (certbot не установлен)
            // После установки certbot можно включить через скрипт setup-ssl-for-subdomain
            
            Log::info('Deploy executed', $response);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Deploy failed', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

