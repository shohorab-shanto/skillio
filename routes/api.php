<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserOnboardingController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\CourseDetailsApiController;
use App\Http\Controllers\Api\MentorDetailsApiController;
use App\Http\Controllers\Api\ReviewApiController;

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

// Home page data endpoints
Route::prefix('home')->group(function () {
    Route::get('top-mentors', [HomeApiController::class, 'getTopMentors']);
    Route::get('popular-courses', [HomeApiController::class, 'getPopularCourses']);
    Route::get('new-courses', [HomeApiController::class, 'getNewCourses']);
    Route::get('featured-courses', [HomeApiController::class, 'getFeaturedCourses']);
    Route::get('top-reviews', [HomeApiController::class, 'getTopReviews']);
    Route::get('all-data', [HomeApiController::class, 'getAllHomeData']);
});

// Course details endpoints
Route::prefix('courses')->group(function () {
    Route::get('{course}/details', [CourseDetailsApiController::class, 'getCourseDetails']);
    Route::get('{course}/reviews', [CourseDetailsApiController::class, 'getCourseReviews']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('{course}/enrolled-students', [CourseDetailsApiController::class, 'getEnrolledStudents']);
        Route::get('{course}/statistics', [CourseDetailsApiController::class, 'getCourseStatistics']);
    });
});

// Mentor details endpoints
Route::prefix('mentors')->group(function () {
    Route::get('{mentor}/details', [MentorDetailsApiController::class, 'getMentorDetails']);
    Route::get('{mentor}/sessions', [MentorDetailsApiController::class, 'getMentorSessions']);
    Route::get('{mentor}/reviews', [MentorDetailsApiController::class, 'getMentorReviews']);
    Route::get('{mentor}/courses', [MentorDetailsApiController::class, 'getMentorCourses']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('{mentor}/statistics', [MentorDetailsApiController::class, 'getMentorStatistics']);
    });
});
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

    // Review endpoints
    Route::prefix('reviews')->group(function () {
        Route::post('/', [ReviewApiController::class, 'store']);
        Route::get('/', [ReviewApiController::class, 'getUserReviews']);
        Route::get('can-review', [ReviewApiController::class, 'canReview']);
        Route::get('{review}', [ReviewApiController::class, 'show']);
        Route::put('{review}', [ReviewApiController::class, 'update']);
        Route::delete('{review}', [ReviewApiController::class, 'destroy']);
    });
});
