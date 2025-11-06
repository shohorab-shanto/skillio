<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserOnboardingController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\CourseDetailsApiController;
use App\Http\Controllers\Api\MentorDetailsApiController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\CourseEnrollmentApiController;
use App\Http\Controllers\Api\SessionBookingApiController;
use App\Http\Controllers\Api\UserDashboardApiController;
use App\Http\Controllers\Api\UserCoursesApiController;
use App\Http\Controllers\Api\UserSessionsApiController;
use App\Http\Controllers\Api\UserPaymentsApiController;
use App\Http\Controllers\Api\UserProfileApiController;
use App\Http\Controllers\Api\UserConversationsApiController;
use App\Http\Controllers\Api\UserNotificationsApiController;
use App\Http\Controllers\Api\MentorDashboardApiController;
use App\Http\Controllers\Api\MentorCoursesApiController;
use App\Http\Controllers\Api\MentorTimeSlotsApiController;
use App\Http\Controllers\Api\MentorEarningsApiController;
use App\Http\Controllers\Api\MentorProfileApiController;
use App\Http\Controllers\Api\MentorReviewsApiController;
use App\Http\Controllers\Api\MentorStudentsApiController;
use App\Http\Controllers\Api\CourseApiController;
use App\Http\Controllers\Api\MentorApiController;
use App\Http\Controllers\Api\SliderApiController;

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
        Route::post('social/google', [AuthController::class, 'googleAuth']); // For web (direct Google OAuth)
        Route::post('firebase/google', [AuthController::class, 'firebaseGoogleAuth']); // For mobile (Firebase)
        Route::post('social/apple', [AuthController::class, 'appleAuth']);
        Route::post('firebase/apple', [AuthController::class, 'firebaseAppleAuth']); // For mobile (Firebase)
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    // Public onboarding data
    Route::get('onboarding/categories', [UserOnboardingController::class, 'getCategories']);
    Route::get('onboarding/countries', [UserOnboardingController::class, 'getCountries']);

// All courses and mentors endpoints
Route::get('courses', [CourseApiController::class, 'getAllCourses']);
Route::get('courses/filter-options', [CourseApiController::class, 'getFilterOptions']);
Route::get('mentors', [MentorApiController::class, 'getAllMentors']);
Route::get('mentors/filter-options', [MentorApiController::class, 'getFilterOptions']);

// Sliders endpoint
Route::get('sliders', [SliderApiController::class, 'index']);
Route::get('sliders/{slider}', [SliderApiController::class, 'show']);

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

    // Course enrollment endpoints
    Route::prefix('enrollments')->group(function () {
        Route::get('courses/{course}/info', [CourseEnrollmentApiController::class, 'getEnrollmentInfo']);
        Route::post('courses/{course}/enroll', [CourseEnrollmentApiController::class, 'enroll']);
        // New Two-Step Payment Flow
        Route::post('courses/{course}/create-payment-intent', [CourseEnrollmentApiController::class, 'createPaymentIntent']);
        Route::post('courses/{course}/confirm-enrollment', [CourseEnrollmentApiController::class, 'confirmEnrollment']);
        Route::get('my-enrollments', [CourseEnrollmentApiController::class, 'getUserEnrollments']);
        Route::get('{enrollment}', [CourseEnrollmentApiController::class, 'getEnrollmentDetails']);
        Route::post('{enrollment}/cancel', [CourseEnrollmentApiController::class, 'cancelEnrollment']);
    });

    // Session booking endpoints
    Route::prefix('sessions')->group(function () {
        Route::get('available', [SessionBookingApiController::class, 'getAvailableSessions']);
        Route::get('{session}/info', [SessionBookingApiController::class, 'getBookingInfo']);
        Route::post('{session}/book', [SessionBookingApiController::class, 'bookSession']);
        // New Two-Step Payment Flow
        Route::post('{session}/create-payment-intent', [SessionBookingApiController::class, 'createPaymentIntent']);
        Route::post('{session}/confirm-booking', [SessionBookingApiController::class, 'confirmBooking']);
        Route::get('my-bookings', [SessionBookingApiController::class, 'getUserBookings']);
        Route::get('bookings/{enrollment}', [SessionBookingApiController::class, 'getBookingDetails']);
        Route::post('bookings/{enrollment}/cancel', [SessionBookingApiController::class, 'cancelBooking']);
        Route::get('bookings/{enrollment}/switchable', [SessionBookingApiController::class, 'getSwitchableSessions']);
        Route::post('bookings/{enrollment}/switch', [SessionBookingApiController::class, 'switchSession']);
    });

    // User Dashboard endpoints
    Route::prefix('user')->group(function () {
        // Dashboard overview
        Route::get('dashboard', [UserDashboardApiController::class, 'index']);
        Route::get('dashboard/statistics', [UserDashboardApiController::class, 'statistics']);
        
        // User courses management
        Route::prefix('courses')->group(function () {
            Route::get('/', [UserCoursesApiController::class, 'index']);
            Route::get('statistics', [UserCoursesApiController::class, 'statistics']);
            Route::get('{enrollment}', [UserCoursesApiController::class, 'show']);
        });
        
        // User sessions management
        Route::prefix('sessions')->group(function () {
            Route::get('/', [UserSessionsApiController::class, 'index']);
            Route::get('statistics', [UserSessionsApiController::class, 'statistics']);
            Route::get('{enrollment}', [UserSessionsApiController::class, 'show']);
            Route::get('{enrollment}/available-slots', [UserSessionsApiController::class, 'getAvailableSlots']);
            Route::post('{enrollment}/switch', [UserSessionsApiController::class, 'switchSession']);
        });
        
        // Payment history
        Route::prefix('payments')->group(function () {
            Route::get('/', [UserPaymentsApiController::class, 'index']);
            Route::get('statistics', [UserPaymentsApiController::class, 'statistics']);
            Route::get('{payment}', [UserPaymentsApiController::class, 'show']);
        });
        
        // User profile management
        Route::prefix('profile')->group(function () {
            Route::get('/', [UserProfileApiController::class, 'show']);
            Route::put('/', [UserProfileApiController::class, 'update']);
            Route::put('password', [UserProfileApiController::class, 'updatePassword']);
            Route::get('preferences', [UserProfileApiController::class, 'getPreferences']);
            Route::put('preferences', [UserProfileApiController::class, 'updatePreferences']);
            Route::get('onboarding-status', [UserProfileApiController::class, 'getOnboardingStatus']);
        });
        
        // Account deletion
        Route::post('delete-account', [UserProfileApiController::class, 'deleteAccount']);
        
        // Chat/Conversation management
        Route::prefix('conversations')->group(function () {
            Route::get('/', [UserConversationsApiController::class, 'index']);
            Route::get('{code}', [UserConversationsApiController::class, 'show']);
            Route::get('{code}/messages', [UserConversationsApiController::class, 'getMessages']);
            Route::post('{code}/messages', [UserConversationsApiController::class, 'sendMessage']);
            Route::get('{code}/chat-status', [UserConversationsApiController::class, 'getChatStatus']);
        });
        
        // Notifications management
        Route::prefix('notifications')->group(function () {
            Route::get('/', [UserNotificationsApiController::class, 'index']);
            Route::get('recent', [UserNotificationsApiController::class, 'getRecent']);
            Route::get('unread-count', [UserNotificationsApiController::class, 'getUnreadCount']);
            Route::put('{notification}/read', [UserNotificationsApiController::class, 'markAsRead']);
            Route::put('read-all', [UserNotificationsApiController::class, 'markAllAsRead']);
            Route::delete('{notification}', [UserNotificationsApiController::class, 'destroy']);
            Route::delete('all', [UserNotificationsApiController::class, 'destroyAll']);
        });
    });

    // Mentor Dashboard endpoints
    Route::prefix('mentor')->group(function () {
        // Dashboard overview
        Route::get('dashboard', [MentorDashboardApiController::class, 'index']);
        Route::get('dashboard/statistics', [MentorDashboardApiController::class, 'statistics']);
        
        // Mentor courses management
        Route::prefix('courses')->group(function () {
            Route::get('/', [MentorCoursesApiController::class, 'index']);
            Route::post('/', [MentorCoursesApiController::class, 'store']);
            Route::get('form-data', [MentorCoursesApiController::class, 'getFormData']);
            Route::get('filter-options', [MentorCoursesApiController::class, 'getFilterOptions']);
            Route::get('statistics', [MentorCoursesApiController::class, 'statistics']);
            Route::get('{course}', [MentorCoursesApiController::class, 'show']);
            Route::get('{course}/students', [MentorCoursesApiController::class, 'getCourseStudents']);
            Route::put('{course}', [MentorCoursesApiController::class, 'update']);
            Route::delete('{course}', [MentorCoursesApiController::class, 'destroy']);
        });
        
        // Mentor time slots management
        Route::prefix('time-slots')->group(function () {
            Route::get('/', [MentorTimeSlotsApiController::class, 'index']);
            Route::post('/', [MentorTimeSlotsApiController::class, 'store']);
            Route::get('form-data', [MentorTimeSlotsApiController::class, 'getFormData']);
            Route::get('statistics', [MentorTimeSlotsApiController::class, 'statistics']);
            Route::get('{time_slot}', [MentorTimeSlotsApiController::class, 'show']);
            Route::put('{time_slot}', [MentorTimeSlotsApiController::class, 'update']);
            Route::delete('{time_slot}', [MentorTimeSlotsApiController::class, 'destroy']);
        });
        
        // Mentor earnings history
        Route::prefix('earnings')->group(function () {
            Route::get('/', [MentorEarningsApiController::class, 'index']);
            Route::get('statistics', [MentorEarningsApiController::class, 'statistics']);
            Route::get('{payment}', [MentorEarningsApiController::class, 'show']);
        });
        
        // Mentor profile management
        Route::prefix('profile')->group(function () {
            Route::get('/', [MentorProfileApiController::class, 'show']);
            Route::put('/', [MentorProfileApiController::class, 'update']);
            Route::put('password', [MentorProfileApiController::class, 'updatePassword']);
            Route::put('status', [MentorProfileApiController::class, 'updateStatus']);
            Route::get('statistics', [MentorProfileApiController::class, 'getStatistics']);
        });
        
        // Mentor reviews management
        Route::prefix('reviews')->group(function () {
            Route::get('/', [MentorReviewsApiController::class, 'index']);
            Route::get('statistics', [MentorReviewsApiController::class, 'statistics']);
            Route::get('rating/{rating}', [MentorReviewsApiController::class, 'getByRating']);
            Route::get('{review}', [MentorReviewsApiController::class, 'show']);
        });
        
        // Mentor students management
        Route::prefix('students')->group(function () {
            Route::get('/', [MentorStudentsApiController::class, 'index']);
            Route::get('statistics', [MentorStudentsApiController::class, 'statistics']);
            Route::get('{student}', [MentorStudentsApiController::class, 'show']);
        });
    });
});
