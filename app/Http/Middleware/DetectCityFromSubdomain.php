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
        
        cookie()->queue('selectedCity', $city, 525600);
        
        return $next($request);
    }
}

