<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

Route::get('/admin/login', function () {
    return view('admin.adminlog');
})->name('admin.login');

Route::post('/admin/verify', function () {
    $pin = request('pin');
    
    if (!$pin || strlen($pin) !== 6) {
        return redirect()->back()->with('error', 'Please enter a valid 6-digit PIN');
    }
    
    $user = User::where('pin', $pin)
                ->where('is_active', true)
                ->whereIn('role', ['admin', 'beach_owner'])
                ->first();
    
    if (!$user) {
        return redirect()->back()->with('error', 'Invalid PIN or unauthorized access');
    }
    
    Auth::login($user);
    
    return redirect('/admin/locations');
})->name('admin.verify');

// Super Admin Login
Route::middleware('superadmin.guest')->group(function () {
    Route::get('/superadmin/login', function () {
        return view('superadmin.login');
    })->name('superadmin.login');

    Route::post('/superadmin/verify', function () {
        $pin = request('pin');
        
        if (!$pin) {
            return redirect()->back()->with('error', 'Please enter your PIN');
        }
        
        if (strlen($pin) !== 6) {
            return redirect()->back()->with('error', 'PIN must be exactly 6 digits');
        }
        
        $user = User::where('pin', $pin)
                    ->where('role', 'super_admin')
                    ->where('is_active', true)
                    ->first();
        
        if (!$user) {
            return redirect()->back()->with('error', 'Invalid Super Admin credentials');
        }
        
        Auth::login($user, request()->has('remember'));
        
        return redirect('/superadmin/dashboard');
    })->name('superadmin.verify');
});

use App\Http\Controllers\LocationController;
use App\Http\Controllers\WeatherController;

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/locations', [LocationController::class, 'index']);
    Route::post('/locations', [LocationController::class, 'store']);
    Route::get('/locations/{location}/edit', [LocationController::class, 'edit']);
    Route::put('/locations/{location}', [LocationController::class, 'update']);
    Route::delete('/locations/{location}', [LocationController::class, 'destroy']);
});

// Super Admin Routes
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/admins', [SuperAdminController::class, 'admins'])->name('superadmin.admins');
    Route::get('/admins/{admin}', [SuperAdminController::class, 'adminDetails'])->name('superadmin.admins.details');
    Route::post('/admins/{admin}/toggle', [SuperAdminController::class, 'toggleAdminStatus'])->name('superadmin.admins.toggle');
    Route::delete('/admins/{admin}', [SuperAdminController::class, 'deleteAdmin'])->name('superadmin.admins.delete');
    Route::post('/admins/{admin}/reset-pin', [SuperAdminController::class, 'resetAdminPin'])->name('superadmin.admins.reset-pin');
    
    Route::get('/users', [SuperAdminController::class, 'users'])->name('superadmin.users');
    
    Route::get('/applications', [SuperAdminController::class, 'applications'])->name('superadmin.applications');
    Route::post('/applications/{application}/approve', [SuperAdminController::class, 'approveApplication'])->name('superadmin.applications.approve');
    Route::post('/applications/{application}/reject', [SuperAdminController::class, 'rejectApplication'])->name('superadmin.applications.reject');
    
    Route::get('/activity-logs', [SuperAdminController::class, 'activityLogs'])->name('superadmin.activity-logs');
    Route::get('/weather-data', [SuperAdminController::class, 'weatherData'])->name('superadmin.weather-data');
});

// API endpoint for frontend to fetch saved locations
Route::get('/api/locations', [LocationController::class, 'apiIndex']);
Route::get('/api/locations/{id}', [LocationController::class, 'apiShow']);

// Weather (OpenWeather) — server-side proxy so API key is not exposed
Route::get('/api/weather', [WeatherController::class, 'current']);
Route::get('/api/weather/comprehensive', [WeatherController::class, 'comprehensive']);
Route::get('/api/weather/forecast', [WeatherController::class, 'forecast']);
