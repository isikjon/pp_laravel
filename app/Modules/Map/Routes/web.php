<?php

use App\Modules\Map\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/map', [MapController::class, 'index'])->name('map');

