<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\MetroController;
use App\Http\Controllers\SearchController;

Route::post('/contact-request', [ContactRequestController::class, 'store'])->name('contact.store');
Route::get('/api/metro/list', [MetroController::class, 'getMetroList'])->name('metro.list');
Route::get('/api/metro/girls', [MetroController::class, 'getGirlsByMetro'])->name('metro.girls');
Route::get('/api/search', [SearchController::class, 'search'])->name('search');
