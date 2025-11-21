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
            // Деплой nginx конфига через shell_exec для лучшего вывода
            $deployOutput = shell_exec('/usr/bin/sudo /usr/local/bin/deploy-nginx-config 2>&1');
            
            // Проверяем что файлы действительно скопировались
            $targetFile = '/etc/nginx/vhosts/noviysayt/' . basename($subdomain ?: 'prostitutkimoskvytake.org') . '.conf';
            $fileExists = file_exists($targetFile);
            
            $response = [
                'success' => $fileExists,
                'deploy' => [
                    'status' => $fileExists ? 'success' : 'error',
                    'output' => $deployOutput ?: 'Команда выполнена, но вывод пуст',
                    'file_check' => $fileExists ? "✓ Файл {$targetFile} создан" : "✗ Файл {$targetFile} не найден"
                ],
                'debug' => [
                    'subdomain' => $subdomain,
                    'target_file' => $targetFile,
                    'php_user' => shell_exec('whoami'),
                    'sudo_works' => shell_exec('sudo -n true 2>&1; echo $?')
                ]
            ];
            
            // Настройка SSL если указан поддомен (пропускаем, т.к. certbot не установлен)
            if ($subdomain && $fileExists && false) { // Отключено временно
                $sslOutput = shell_exec("/usr/bin/sudo /usr/local/bin/setup-ssl-for-subdomain {$subdomain} 2>&1");
                
                $response['ssl'] = [
                    'status' => 'skipped',
                    'output' => 'SSL настройка отключена (certbot не установлен)'
                ];
            }
            
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

