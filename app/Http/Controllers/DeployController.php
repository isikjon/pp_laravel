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
            // Деплой nginx конфига
            exec('/usr/bin/sudo /usr/local/bin/deploy-nginx-config 2>&1', $deployOutput, $deployReturn);
            
            $response = [
                'success' => $deployReturn === 0,
                'deploy' => [
                    'status' => $deployReturn === 0 ? 'success' : 'error',
                    'output' => implode("\n", $deployOutput ?? [])
                ]
            ];
            
            // Настройка SSL если указан поддомен
            if ($subdomain && $deployReturn === 0) {
                exec("/usr/bin/sudo /usr/local/bin/setup-ssl-for-subdomain {$subdomain} 2>&1", $sslOutput, $sslReturn);
                
                $response['ssl'] = [
                    'status' => $sslReturn === 0 ? 'success' : 'error',
                    'output' => implode("\n", $sslOutput ?? [])
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

