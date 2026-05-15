<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ObstacleController;
use App\Http\Controllers\PlaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/map', [MapController::class, 'index'])->name('map.index');

Route::get('/api/places', [PlaceController::class, 'index'])->name('api.places');
Route::get('/api/obstacles', [ObstacleController::class, 'index'])->name('api.obstacles');