<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheResponseHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        if (! $this->isCacheable($request, $response)) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'public, max-age=3600, stale-while-revalidate=60');

        return $response;
    }

    protected function isCacheable(Request $request, Response $response): bool
    {
        if (! $request->isMethodCacheable() || $response->isRedirection()) {
            return false;
        }

        if ($response->headers->has('Cache-Control')) {
            return false;
        }

        return $response->getStatusCode() === 200;
    }
}

