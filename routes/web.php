<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExemptionController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\User;


Route::redirect('/', 'login')->name('home');

Route::get('/login-as-user/{id}', function ($id) {
    abort_if(auth()->user()->role !== 'admin', 403);
    $user = User::find($id);
    auth()->login($user);
    return redirect()->route('dashboard');
})->name('login-as-user');


Route::middleware(['auth'])->group(function () {
    
    Route::get('dashboard',[DashboardController::class,'index'])->name('dashboard');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/signature', 'settings.signature')->name('settings.signature');
    Volt::route('settings/stamp', 'settings.stamp')->name('settings.stamp');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Departments
    Route::apiResource('departments', DepartmentController::class);
    
    // Users
    Route::apiResource('users', UserController::class);


    Route::middleware(['signature'])->group(function () {
        
        // Missions
        Route::controller(MissionController::class)->group(function () {
            Route::post('missions/{mission}/change-status', 'changeStatus');
            Route::post('missions/mass-approve', 'massApprove');
        });
        Route::apiResource('missions', MissionController::class);

        // Permissions
        Route::controller(PermissionController::class)->group(function () {
            Route::post('permissions/{permission}/change-status', 'changeStatus');
            Route::post('permissions/mass-approve', 'massApprove');
        });
        Route::apiResource('permissions', PermissionController::class);
    
        // Exemptions
        Route::controller(ExemptionController::class)->group(function () {
            Route::post('exemptions/{exemption}/change-status', 'changeStatus');
            Route::post('exemptions/mass-approve', 'massApprove');
        });
        Route::apiResource('exemptions', ExemptionController::class);
        
        // Requests
        Route::controller(RequestController::class)->group(function () {
            Route::get('requests/counts', 'getCounts');
            Route::get('requests/missions', 'missions')->name('requests.missions');
            Route::get('requests/permissions', 'permissions')->name('requests.permissions');
            Route::get('requests/exemptions', 'exemptions')->name('requests.exemptions');
        });
    });

    
});

require __DIR__.'/auth.php';
