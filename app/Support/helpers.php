<?php

use Illuminate\Support\Facades\File;

if (! function_exists('cached_asset')) {
    function cached_asset(string $path): string
    {
        $fullPath = public_path($path);
        $version = File::exists($fullPath) ? File::lastModified($fullPath) : time();

        return asset($path) . '?v=' . $version;
    }
}

if (! function_exists('getSelectedCity')) {
    function getSelectedCity(?Illuminate\Http\Request $request = null): string
    {
        $request = $request ?? request();
        
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        
        if ($subdomain === 'spb') {
            return 'spb';
        }
        
        if ($subdomain === 'www' || $subdomain === 'prostitutkitest') {
            return 'moscow';
        }
        
        $city = $request->cookie('selectedCity', 'moscow');
        
        if (!in_array($city, ['moscow', 'spb'])) {
            return 'moscow';
        }
        
        return $city;
    }
}

