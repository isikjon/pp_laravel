<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemoveCityFromUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('city')) {
            $queryParams = $request->query();
            unset($queryParams['city']);
            
            $url = $request->url();
            if (!empty($queryParams)) {
                $url .= '?' . http_build_query($queryParams);
            }
            
            return redirect($url, 301);
        }
        
        return $next($request);
    }
}

