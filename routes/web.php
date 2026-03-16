<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('index');
});

Route::get('/landing', function () {
    return view('index');
});

Route::get('/explore', function () {
    return view('landing');
})->name('explore');

// Auth: guest-only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin locations management
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WeatherController;

Route::get('/admin/locations', [LocationController::class, 'index']);
Route::post('/admin/locations', [LocationController::class, 'store']);
Route::get('/admin/locations/{location}/edit', [LocationController::class, 'edit']);
Route::put('/admin/locations/{location}', [LocationController::class, 'update']);
Route::delete('/admin/locations/{location}', [LocationController::class, 'destroy']);

// API endpoint for frontend to fetch saved locations
Route::get('/api/locations', [LocationController::class, 'apiIndex']);
Route::get('/api/locations/{id}', [LocationController::class, 'apiShow']);

// Weather (OpenWeather) — server-side proxy so API key is not exposed
Route::get('/api/weather', [WeatherController::class, 'current']);
