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

