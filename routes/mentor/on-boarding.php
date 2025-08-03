<?php

Route::middleware(['set_locale'])->group(function () {
    Route::get('/mentor/login', [App\Http\Controllers\MentorOnBoardingController::class, 'login'])->name('mentor.onboarding.login');
    Route::get('/mentor/register', [App\Http\Controllers\MentorOnBoardingController::class, 'register'])->name('mentor.onboarding.register');
});
