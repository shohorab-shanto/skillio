<?php



Route::middleware(['user_auth', 'onboarding_complete'])->group(function () {
    Route::get('/user/dashboard', [App\Http\Controllers\backend\user\DashboardController::class, 'index'])->name('user.dashboard');
});
