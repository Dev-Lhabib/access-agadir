<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ObstacleController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/map', [MapController::class, 'index'])->name('map.index');

Route::get('/api/places', [PlaceController::class, 'index'])->name('api.places');
Route::get('/places/{id}', [PlaceController::class, 'show'])->name('places.show');
Route::post('/places/{id}/reviews', [ReviewController::class, 'store'])->name('places.reviews.store');
Route::get('/api/obstacles', [ObstacleController::class, 'index'])->name('api.obstacles');
Route::post('/ai/recommend', [AiController::class, 'recommend'])->name('ai.recommend');