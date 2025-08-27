
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ChatController;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/mentors', [App\Http\Controllers\MentorsController::class, 'index'])->name('mentors');
Route::get('/mentors/{mentor}/profile-and-sessions', [App\Http\Controllers\MentorSessionController::class, 'show'])->name('mentor.sessions');
Route::get('/courses', [App\Http\Controllers\CoursesController::class, 'index'])->name('courses');
Route::get('/courses/{course}', [App\Http\Controllers\CoursesController::class, 'show'])->name('courses.show');

// Legal Pages
Route::get('/terms-and-conditions', function () {
    return view('frontend.terms-and-conditions');
})->name('terms-and-conditions');

Route::get('/privacy-policy', function () {
    return view('frontend.privacy-policy');
})->name('privacy-policy');

// Search Routes
Route::get('/search', [App\Http\Controllers\SearchController::class, 'search'])->name('search');
Route::get('/search/suggestions', [App\Http\Controllers\SearchController::class, 'suggestions'])->name('search.suggestions');

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->name('notifications.recent');
    Route::get('/notifications/count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');
    Route::patch('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::patch('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

// Checkout Routes - Generic for both sessions and courses
Route::middleware(['user_auth'])->group(function () {
    Route::get('/checkout/session/{sessionBooking}', [App\Http\Controllers\CheckoutController::class, 'sessionCheckout'])->name('checkout.session');
    Route::post('/checkout/session/{sessionBooking}/process', [App\Http\Controllers\CheckoutController::class, 'processSessionPayment'])->name('checkout.session.process');
    Route::get('/checkout/course/{course}', [App\Http\Controllers\CheckoutController::class, 'courseCheckout'])->name('checkout.course');
    Route::post('/checkout/course/{course}/process', [App\Http\Controllers\CheckoutController::class, 'processCoursePayment'])->name('checkout.course.process');
    
    // Payment result pages
    Route::get('/payment/success', [App\Http\Controllers\PaymentResultController::class, 'success'])->name('payment.success');
    Route::get('/payment/failure', [App\Http\Controllers\PaymentResultController::class, 'failure'])->name('payment.failure');
});

// Stripe Webhook Route (no middleware - Stripe needs direct access)
Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// Webhook test route for development (remove in production)
Route::get('/stripe/webhook/test', [App\Http\Controllers\StripeWebhookController::class, 'testWebhook'])->name('stripe.webhook.test');

// WebSocket Connection Test (for development)
Route::get('/test-websocket', function () {
    return view('test-websocket');
})->name('test.websocket');

Route::get('/test-realtime', function () {
    return view('test-realtime');
})->name('test.realtime');

// Debug route to check authentication
Route::get('/debug/auth', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? auth()->user()->only(['id', 'name', 'email']) : null,
        'session_id' => session()->getId(),
    ]);
})->name('debug.auth');

require base_path('/routes/user/on-boarding.php');
require base_path('/routes/user/dashboard.php');
require base_path('/routes/mentor/on-boarding.php');
require base_path('/routes/mentor/dashboard.php');

// Chat Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{code}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{code}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/create', [ChatController::class, 'getOrCreateConversation'])->name('chat.create');
    
    // API route for user search
    Route::get('/api/users/search', [ChatController::class, 'searchUsers'])->name('api.users.search');
});

Route::post('/lang/switch', [LanguageController::class, 'switch'])->name('lang.switch');
Route::get('/lang/{lang}', [LanguageController::class, 'switchLanguage'])->name('language.switch');
// Google
Route::get('/login/google', [SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('login.google.callback');

// Apple
Route::get('/login/apple', [SocialAuthController::class, 'redirectToApple'])->name('login.apple');
Route::post('/login/apple/callback', [SocialAuthController::class, 'handleAppleCallback'])->name('login.apple.callback'); // Apple often uses POST


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Include debug routes
require __DIR__.'/debug-routes.php';

// Review routes
Route::middleware(['auth'])->group(function () {
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});
