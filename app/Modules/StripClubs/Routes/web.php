<?php

use App\Modules\StripClubs\Controllers\StripClubsController;
use Illuminate\Support\Facades\Route;

Route::get('/strip-clubs', [StripClubsController::class, 'index'])->name('stripclubs.index');
Route::get('/strip-club/{id}', [StripClubsController::class, 'show'])->name('stripclub.show');
