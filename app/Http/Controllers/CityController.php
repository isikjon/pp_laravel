<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CityController extends Controller
{
    public function setCity(Request $request)
    {
        try {
            $cityCode = $request->input('city', 'moscow');
            
            $city = \App\Models\City::where('code', $cityCode)
                ->where('is_active', true)
                ->first();
            
            if (!$city) {
                $city = \App\Models\City::where('code', 'moscow')->where('is_active', true)->first();
                $cityCode = $city ? $city->code : 'moscow';
            }
            
            cookie()->queue('selectedCity', $cityCode, 525600);
            
            $host = $request->getHost();
            $hostParts = explode('.', $host);
            $currentSubdomain = count($hostParts) > 2 ? $hostParts[0] : null;
            
            $path = $request->getPathInfo();
            if ($path === '/') {
                $path = '';
            }
            
            $protocol = $request->getScheme();
            $query = $request->getQueryString();
            $domain = config('app.domain', 'prostitutkimoskvytake.org');
            
            if ($city && $city->subdomain && $currentSubdomain !== $city->subdomain) {
                $redirectUrl = $protocol . '://' . $city->subdomain . '.' . $domain . $path;
                if ($query) {
                    $redirectUrl .= '?' . $query;
                }
            } elseif ($city && !$city->subdomain && $currentSubdomain) {
                $redirectUrl = $protocol . '://' . $domain . $path;
                if ($query) {
                    $redirectUrl .= '?' . $query;
                }
            } else {
                $redirectUrl = $protocol . '://' . $host . $path;
                if ($query) {
                    $redirectUrl .= '?' . $query;
                }
            }
            
            return response()->json([
                'success' => true,
                'city' => $cityCode,
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

