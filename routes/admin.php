<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminMentorController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "admin" middleware group.
|
*/

// Admin Auth Routes (no middleware)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
});

// Admin Protected Routes
Route::middleware(['admin_auth'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Users Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}/enrollments', [AdminUserController::class, 'enrollments'])->name('admin.users.enrollments');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    
    // Mentors Management
    Route::get('/mentors', [AdminMentorController::class, 'index'])->name('admin.mentors.index');
    Route::get('/mentors/{user}', [AdminMentorController::class, 'show'])->name('admin.mentors.show');
    Route::get('/mentors/{user}/courses', [AdminMentorController::class, 'courses'])->name('admin.mentors.courses');
    Route::get('/mentors/{user}/sessions', [AdminMentorController::class, 'sessions'])->name('admin.mentors.sessions');
    Route::patch('/mentors/{user}/toggle-verification', [AdminMentorController::class, 'toggleVerification'])->name('admin.mentors.toggle-verification');
    Route::patch('/mentors/{user}/update-availability', [AdminMentorController::class, 'updateAvailability'])->name('admin.mentors.update-availability');
});
