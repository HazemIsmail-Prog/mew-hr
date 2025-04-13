<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RequestController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/signature', 'settings.signature')->name('settings.signature');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Departments
    Route::apiResource('departments', DepartmentController::class);
    
    // Users
    Route::apiResource('users', UserController::class);

    // Missions
    Route::post('missions/{mission}/change-status', [MissionController::class, 'changeStatus'])
    ->name('missions.changeStatus')
    ->middleware(['signature', 'arabicName']);

    Route::get('missions/{mission}/pdf', [MissionController::class, 'pdf'])
    ->middleware(['signature', 'arabicName']);

    Route::apiResource('missions', MissionController::class)
    ->middleware(['signature', 'arabicName']);

    // Permissions
    Route::post('permissions/{permission}/change-status', [PermissionController::class, 'changeStatus'])
    ->name('permissions.changeStatus')
    ->middleware(['signature', 'arabicName']);

    Route::get('permissions/{permission}/pdf', [PermissionController::class, 'pdf'])
    ->middleware(['signature', 'arabicName']);

    Route::apiResource('permissions', PermissionController::class)
    ->middleware(['signature', 'arabicName']);
    
    // Requests
    Route::get('requests/missions', [RequestController::class, 'missions'])
    ->middleware(['signature', 'arabicName']);

    Route::get('requests/permissions', [RequestController::class, 'permissions'])
    ->middleware(['signature', 'arabicName']);

    Route::get('requests/counts', [RequestController::class, 'getCounts'])
    ->middleware(['signature', 'arabicName']);

    Route::get('requests', [RequestController::class, 'index'])
    ->middleware(['signature', 'arabicName'])
    ->name('requests.index');
    
});

require __DIR__.'/auth.php';
