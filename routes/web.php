
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SocialAuthController;

Route::get('/', function () {
    return view('welcome');
});

require base_path('/routes/user/on-boarding.php');
require base_path('/routes/user/dashboard.php');
require base_path('/routes/mentor/on-boarding.php');
require base_path('/routes/mentor/dashboard.php');

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
