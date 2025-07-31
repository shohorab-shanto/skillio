<?php

Route::get('/user/login', [App\Http\Controllers\UserOnBoardingController::class, 'login'])->name('user.onboarding.login');
Route::get('/user/register', [App\Http\Controllers\UserOnBoardingController::class, 'register'])->name('user.onboarding.register');

Route::middleware('user_auth')->group(function () {
    Route::get('/user/category-service', [App\Http\Controllers\UserOnBoardingController::class, 'categoryService'])->name('user.onboarding.category_service');
    Route::post('/user/category-service', [App\Http\Controllers\UserOnBoardingController::class, 'categoryServiceSubmit'])->name('user.onboarding.category_service.submit');
    Route::get('/user/in-person-or-online', [App\Http\Controllers\UserOnBoardingController::class, 'inPersonOrOnline'])->name('user.onboarding.in_person_or_online');
    Route::post('/user/in-person-or-online', [App\Http\Controllers\UserOnBoardingController::class, 'inPersonOrOnlineSubmit'])->name('user.onboarding.in_person_or_online.submit');
    Route::get('/user/in-person-education-location', [App\Http\Controllers\UserOnBoardingController::class, 'inPersonEducationLocation'])->name('user.onboarding.in_person_education_location');
    Route::post('/user/in-person-education-location', [App\Http\Controllers\UserOnBoardingController::class, 'inPersonEducationLocationSubmit'])->name('user.onboarding.in_person_education_location.submit');
    Route::get('/user/online-education', [App\Http\Controllers\UserOnBoardingController::class, 'onlineEducation'])->name('user.onboarding.online_education');
    Route::post('/user/online-education', [App\Http\Controllers\UserOnBoardingController::class, 'onlineEducationSubmit'])->name('user.onboarding.online_education.submit');
});