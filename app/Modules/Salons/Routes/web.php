<?php

use App\Modules\Salons\Controllers\SalonsController;
use Illuminate\Support\Facades\Route;

Route::get('/salons', [SalonsController::class, 'index'])->name('salons');
Route::get('/salon/{id}', [SalonsController::class, 'show'])->name('salon.show');

