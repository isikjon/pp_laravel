<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\MetroController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FilterOptionsController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DeployController;

Route::post('/contact-request', [ContactRequestController::class, 'store'])->name('contact.store');
Route::post('/api/city/set', [CityController::class, 'setCity'])->name('city.set');
Route::get('/api/metro/list', [MetroController::class, 'getMetroList'])->name('metro.list');
Route::get('/api/metro/girls', [MetroController::class, 'getGirlsByMetro'])->name('metro.girls');
Route::get('/api/search', [SearchController::class, 'search'])->name('search');
Route::get('/api/filter-options', [FilterOptionsController::class, 'getFilterOptions'])->name('filter.options');

// Deploy API endpoint
Route::post('/api/deploy-config', [DeployController::class, 'deployNginxConfig'])->name('deploy.config');
