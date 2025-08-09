<?php

use App\Http\Controllers\backend\user\ProfileController;
use App\Http\Controllers\backend\user\DashboardController;



Route::middleware(['user_auth', 'onboarding_complete'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');

    // user profile
    Route::get('/user/profile', [ProfileController::class, 'show'])->name('user.profile.show');
    Route::post('/user/profile-update', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::post('/user/password-update', [ProfileController::class, 'updatePassword'])->name('user.profile.updatePassword');
});
