<?php

use App\Modules\Contact\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/contact', [ContactController::class, 'index'])->name('contact');

