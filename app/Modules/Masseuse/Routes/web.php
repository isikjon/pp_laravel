<?php

use App\Modules\Masseuse\Controllers\MasseuseController;
use Illuminate\Support\Facades\Route;

Route::get('/masseuse', [MasseuseController::class, 'index'])->name('masseuse');
Route::get('/masseuse/{id}', [MasseuseController::class, 'show'])->name('masseuse.show');

