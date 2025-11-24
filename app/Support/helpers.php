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

if (! function_exists('getCurrentCity')) {
    function getCurrentCity(?Illuminate\Http\Request $request = null): ?\App\Models\City
    {
        $request = $request ?? request();
        
        $host = $request->getHost();
        $parts = explode('.', $host);
        $subdomain = count($parts) > 2 ? $parts[0] : null;
        
        return \App\Models\City::where('is_active', true)
            ->where(function($query) use ($subdomain) {
                if ($subdomain) {
                    $query->where('subdomain', $subdomain);
                } else {
                    $query->whereNull('subdomain')->orWhere('subdomain', '');
                }
            })
            ->first();
    }
}

if (! function_exists('getSelectedCity')) {
    function getSelectedCity(?Illuminate\Http\Request $request = null): string
    {
        $city = getCurrentCity($request);
        return $city ? $city->code : 'moscow';
    }
}

if (! function_exists('getCityName')) {
    function getCityName(?Illuminate\Http\Request $request = null): string
    {
        $city = getCurrentCity($request);
        return $city ? $city->name : 'Москва';
    }
}

if (! function_exists('getCityNameInCase')) {
    function getCityNameInCase(string $case = 'nominative', ?Illuminate\Http\Request $request = null): string
    {
        $cityName = getCityName($request);
        
        $cases = [
            'Москва' => [
                'nominative' => 'Москва',
                'genitive' => 'Москвы',
                'dative' => 'Москве',
                'accusative' => 'Москву',
                'instrumental' => 'Москвой',
                'prepositional' => 'Москве',
            ],
            'Санкт-Петербург' => [
                'nominative' => 'Санкт-Петербург',
                'genitive' => 'Санкт-Петербурга',
                'dative' => 'Санкт-Петербургу',
                'accusative' => 'Санкт-Петербург',
                'instrumental' => 'Санкт-Петербургом',
                'prepositional' => 'Санкт-Петербурге',
            ],
        ];
        
        if (isset($cases[$cityName][$case])) {
            return $cases[$cityName][$case];
        }
        
        return $cityName;
    }
}

