<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminMentorController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminSubCategoryController;

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
    
    // Courses Management
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
    Route::get('/courses/{course}', [AdminCourseController::class, 'show'])->name('admin.courses.show');
    Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('admin.courses.approve');
    Route::patch('/courses/{course}/reject', [AdminCourseController::class, 'reject'])->name('admin.courses.reject');
    Route::patch('/courses/{course}/toggle-status', [AdminCourseController::class, 'toggleStatus'])->name('admin.courses.toggle-status');
    
    // Categories Management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::get('/categories/{category}/usage-stats', [AdminCategoryController::class, 'getUsageStats'])->name('admin.categories.usage-stats');
    
    // Sub-Categories Management
    Route::get('/sub-categories', [AdminSubCategoryController::class, 'index'])->name('admin.sub-categories.index');
    Route::get('/sub-categories/create', [AdminSubCategoryController::class, 'create'])->name('admin.sub-categories.create');
    Route::post('/sub-categories', [AdminSubCategoryController::class, 'store'])->name('admin.sub-categories.store');
    Route::get('/sub-categories/{subCategory}/edit', [AdminSubCategoryController::class, 'edit'])->name('admin.sub-categories.edit');
    Route::put('/sub-categories/{subCategory}', [AdminSubCategoryController::class, 'update'])->name('admin.sub-categories.update');
    Route::delete('/sub-categories/{subCategory}', [AdminSubCategoryController::class, 'destroy'])->name('admin.sub-categories.destroy');
    Route::get('/sub-categories/{subCategory}/usage-stats', [AdminSubCategoryController::class, 'getUsageStats'])->name('admin.sub-categories.usage-stats');
});
