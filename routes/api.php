<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserOnboardingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('social/google', [AuthController::class, 'googleAuth']);
        Route::post('social/apple', [AuthController::class, 'appleAuth']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Public onboarding data
    Route::get('onboarding/categories', [UserOnboardingController::class, 'getCategories']);
    Route::get('onboarding/countries', [UserOnboardingController::class, 'getCountries']);
});

// Protected routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // User onboarding
    Route::prefix('onboarding')->group(function () {
        Route::post('category-selection', [UserOnboardingController::class, 'saveCategorySelection']);
        Route::post('education-type', [UserOnboardingController::class, 'saveEducationType']);
        Route::post('location', [UserOnboardingController::class, 'saveLocation']);
        Route::post('online-options', [UserOnboardingController::class, 'saveOnlineOptions']);
        Route::get('status', [UserOnboardingController::class, 'getOnboardingStatus']);
        Route::get('preferences', [UserOnboardingController::class, 'getUserPreferences']);
        Route::get('my-custom-subcategories', [UserOnboardingController::class, 'getMyCustomSubCategories']);
    });
});
