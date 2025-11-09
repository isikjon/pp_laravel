<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class AssetController extends Controller
{
    public function css(string $path): Response
    {
        return $this->serve(public_path('css/' . $path), 'text/css');
    }

    public function js(string $path): Response
    {
        return $this->serve(public_path('js/' . $path), 'application/javascript');
    }

    public function image(string $path): Response
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => File::mimeType(public_path('img/' . $path)),
        };

        return $this->serve(public_path('img/' . $path), $mime);
    }

    protected function serve(string $fullPath, string $mime): Response
    {
        abort_unless(File::exists($fullPath), 404);

        $content = File::get($fullPath);
        $response = response($content, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);

        $etag = sha1($content);
        $response->setEtag($etag);

        return $response;
    }
}

