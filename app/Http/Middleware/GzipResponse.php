<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GzipResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin/*') || $request->is('admin') || $request->is('livewire/*')) {
            return $next($request);
        }

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        if (! $this->shouldCompress($request, $response)) {
            return $response;
        }

        $compressed = gzencode($response->getContent(), 9);

        $response->setContent($compressed);
        $response->headers->set('Content-Encoding', 'gzip');
        $response->headers->set('Vary', 'Accept-Encoding');
        $response->headers->set('Content-Length', strlen($compressed));

        return $response;
    }

    protected function shouldCompress(Request $request, Response $response): bool
    {
        if (! $request->headers->has('Accept-Encoding') || stripos($request->header('Accept-Encoding'), 'gzip') === false) {
            return false;
        }

        if ($response->headers->has('Content-Encoding')) {
            return false;
        }

        $content = $response->getContent();
        if ($content === '' || $content === null) {
            return false;
        }

        $type = $response->headers->get('Content-Type', '');

        $compressible = [
            'text/',
            'application/javascript',
            'application/json',
            'application/xml',
            'image/svg+xml',
        ];

        foreach ($compressible as $needle) {
            if (stripos($type, $needle) === 0) {
                return true;
            }
        }

        return false;
    }
}

