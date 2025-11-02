<?php

use Illuminate\Support\Facades\Route;
use App\Modules\IntimMap\Controllers\IntimMapController;

Route::prefix('intim-map')->group(function () {
    Route::get('/', [IntimMapController::class, 'index'])->name('intimmap.index');
    Route::get('/data', [IntimMapController::class, 'getMapData'])->name('intimmap.data');
});

