<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CityController extends Controller
{
    public function setCity(Request $request)
    {
        try {
            $city = $request->input('city', 'moscow');
            
            if (!in_array($city, ['moscow', 'spb'])) {
                $city = 'moscow';
            }
            
            cookie()->queue('selectedCity', $city, 525600);
            
            $host = $request->getHost();
            $currentSubdomain = explode('.', $host)[0];
            
            $path = $request->getPathInfo();
            if ($path === '/') {
                $path = '';
            }
            
            $redirectUrl = null;
            
            if ($city === 'spb' && $currentSubdomain !== 'spb') {
                $protocol = $request->getScheme();
                $redirectUrl = $protocol . '://spb.prostitutkitest.com' . $path;
                $query = $request->getQueryString();
                if ($query) {
                    $redirectUrl .= '?' . $query;
                }
            } elseif ($city === 'moscow' && $currentSubdomain === 'spb') {
                $protocol = $request->getScheme();
                $redirectUrl = $protocol . '://prostitutkitest.com' . $path;
                $query = $request->getQueryString();
                if ($query) {
                    $redirectUrl .= '?' . $query;
                }
            } else {
                $redirectUrl = $request->fullUrl();
            }
            
            return response()->json([
                'success' => true,
                'city' => $city,
                'redirect_url' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            Log::error('CityController error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

