<?php

use Illuminate\Support\Facades\File;

if (! function_exists('cached_asset')) {
    function cached_asset(string $path): string
    {
        $version = File::exists(public_path($path)) ? File::lastModified(public_path($path)) : time();

        if (str_starts_with($path, 'css/')) {
            return route('assets.css', ['path' => substr($path, 4)]) . '?v=' . $version;
        }

        if (str_starts_with($path, 'js/')) {
            return route('assets.js', ['path' => substr($path, 3)]) . '?v=' . $version;
        }

        if (str_starts_with($path, 'img/')) {
            return route('assets.img', ['path' => substr($path, 4)]) . '?v=' . $version;
        }

        return asset($path) . '?v=' . $version;
    }
}

