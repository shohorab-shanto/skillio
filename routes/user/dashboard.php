<?php

use App\Http\Controllers\backend\user\ProfileController;
use App\Http\Controllers\backend\user\DashboardController;
use App\Http\Controllers\backend\user\UserSessionsController;
use App\Http\Controllers\backend\user\UserCoursesController;
use App\Http\Controllers\backend\user\PaymentHistoryController;



Route::middleware(['user_auth', 'onboarding_complete', 'set_locale'])->group(function () {
    Route::get('/user/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');

    // user sessions
    Route::get('/user/sessions', [UserSessionsController::class, 'index'])->name('user.sessions');
    Route::get('/user/sessions/{enrollment}', [UserSessionsController::class, 'show'])->name('user.sessions.show');
    Route::get('/user/sessions/{enrollment}/available-slots', [UserSessionsController::class, 'getAvailableSlots'])->name('user.sessions.available-slots');
    Route::post('/user/sessions/{enrollment}/switch', [UserSessionsController::class, 'switchSession'])->name('user.sessions.switch');
    
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
