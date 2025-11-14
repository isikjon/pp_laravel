<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectCityFromSubdomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $city = getSelectedCity($request);
        
        if (!$request->hasCookie('selectedCity') || $request->cookie('selectedCity') !== $city) {
            cookie()->queue('selectedCity', $city, 525600);
        }
        
        return $next($request);
    }
}

