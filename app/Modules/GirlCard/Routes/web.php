<?php

use App\Modules\GirlCard\Controllers\GirlCardController;
use Illuminate\Support\Facades\Route;

Route::get('/girl/{id}', [GirlCardController::class, 'show'])->name('girl.show');

