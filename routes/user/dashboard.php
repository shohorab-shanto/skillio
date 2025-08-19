<?php

use App\Http\Controllers\backend\user\ProfileController;
use App\Http\Controllers\backend\user\DashboardController;
use App\Http\Controllers\backend\user\UserSessionsController;
use App\Http\Controllers\backend\user\UserCoursesController;
use App\Http\Controllers\backend\user\PaymentHistoryController;



Route::middleware(['user_auth', 'onboarding_complete'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');

    // user sessions
    Route::get('/user/sessions', [UserSessionsController::class, 'index'])->name('user.sessions');
    Route::get('/user/sessions/{enrollment}', [UserSessionsController::class, 'show'])->name('user.sessions.show');
    
    // user courses
    Route::get('/user/courses', [UserCoursesController::class, 'index'])->name('user.courses');
    Route::get('/user/courses/{enrollment}', [UserCoursesController::class, 'show'])->name('user.courses.show');
    
    // user payment history
    Route::get('/user/payments', [PaymentHistoryController::class, 'index'])->name('user.payments');
    
    // user profile
    Route::get('/user/profile', [ProfileController::class, 'show'])->name('user.profile.show');
    Route::post('/user/profile-update', [ProfileController::class, 'update'])->name('user.profile.update');
    Route::post('/user/password-update', [ProfileController::class, 'updatePassword'])->name('user.profile.updatePassword');
});
