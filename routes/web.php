<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\MetroController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FilterOptionsController;
use App\Http\Controllers\AssetController;

Route::get('/assets/css/{path}', [AssetController::class, 'css'])
    ->where('path', '.*')
    ->name('assets.css');

Route::get('/assets/js/{path}', [AssetController::class, 'js'])
    ->where('path', '.*')
    ->name('assets.js');

Route::get('/assets/img/{path}', [AssetController::class, 'image'])
    ->where('path', '.*')
    ->name('assets.img');

Route::post('/contact-request', [ContactRequestController::class, 'store'])->name('contact.store');
Route::get('/api/metro/list', [MetroController::class, 'getMetroList'])->name('metro.list');
Route::get('/api/metro/girls', [MetroController::class, 'getGirlsByMetro'])->name('metro.girls');
Route::get('/api/search', [SearchController::class, 'search'])->name('search');
Route::get('/api/filter-options', [FilterOptionsController::class, 'getFilterOptions'])->name('filter.options');
