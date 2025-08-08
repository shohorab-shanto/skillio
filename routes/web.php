
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('welcome');
});

require base_path('/routes/user/on-boarding.php');
require base_path('/routes/user/dashboard.php');
require base_path('/routes/mentor/on-boarding.php');
require base_path('/routes/mentor/dashboard.php');

// Chat Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/create', [ChatController::class, 'getOrCreateConversation'])->name('chat.create');
    
    // API route for user search
    Route::get('/api/users/search', [ChatController::class, 'searchUsers'])->name('api.users.search');
});

Route::post('/lang/switch', [LanguageController::class, 'switch'])->name('lang.switch');
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
