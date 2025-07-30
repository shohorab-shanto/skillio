<?php

Route::get('/user/login', [App\Http\Controllers\UserOnBoardingController::class, 'login'])->name('user.onboarding.login');
Route::get('/user/register', [App\Http\Controllers\UserOnBoardingController::class, 'register'])->name('user.onboarding.register');

Route::middleware('user_auth')->group(function () {
    Route::get('/user/category-service', [App\Http\Controllers\UserOnBoardingController::class, 'categoryService'])->name('user.onboarding.category_service');
    Route::get('/user/in-person-or-online', [App\Http\Controllers\UserOnBoardingController::class, 'inPersonOrOnline'])->name('user.onboarding.in_person_or_online');
    Route::get('/user/in-person-education-location', [App\Http\Controllers\UserOnBoardingController::class, 'inPersonEducationLocation'])->name('user.onboarding.in_person_education_location');
    Route::get('/user/online-education', [App\Http\Controllers\UserOnBoardingController::class, 'onlineEducation'])->name('user.onboarding.online_education');
});