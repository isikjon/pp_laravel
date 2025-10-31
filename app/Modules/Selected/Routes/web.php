<?php

use App\Modules\Selected\Controllers\SelectedController;
use Illuminate\Support\Facades\Route;

Route::get('/selected', [SelectedController::class, 'index'])->name('selected');
Route::get('/izbrannoye', [SelectedController::class, 'index'])->name('favorites');

