<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['mentor_auth'])->group(function () {
    Route::get('/mentor/dashboard', [App\Http\Controllers\backend\mentor\DashboardController::class, 'index'])->name('mentor.dashboard');
    // mentor profile
    Route::get('/mentor/profile', [App\Http\Controllers\backend\mentor\ProfileController::class, 'show'])->name('mentor.profile.show');
    Route::post('/mentor/profile-update', [App\Http\Controllers\backend\mentor\ProfileController::class, 'update'])->name('mentor.profile.update');
    Route::post('/mentor/password-update', [App\Http\Controllers\backend\mentor\ProfileController::class, 'updatePassword'])->name('mentor.profile.updatePassword');
    Route::post('/mentor/update-status', [App\Http\Controllers\backend\mentor\ProfileController::class, 'updateStatus'])->name('mentor.profile.updateStatus');
    // mentor reviews
    Route::get('/mentor/reviews', [App\Http\Controllers\backend\mentor\ReviewController::class, 'index'])->name('mentor.reviews.index');
});
