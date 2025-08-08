<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\backend\mentor\DashboardController;
use App\Http\Controllers\backend\mentor\ProfileController;
use App\Http\Controllers\backend\mentor\ReviewController;
use App\Http\Controllers\backend\mentor\CourseController;
use App\Http\Controllers\backend\mentor\TimeSlotController;

Route::middleware(['mentor_auth'])->group(function () {
    Route::get('/mentor/dashboard', [DashboardController::class, 'index'])->name('mentor.dashboard');
    // mentor profile
    Route::get('/mentor/profile', [ProfileController::class, 'show'])->name('mentor.profile.show');
    Route::post('/mentor/profile-update', [ProfileController::class, 'update'])->name('mentor.profile.update');
    Route::post('/mentor/password-update', [ProfileController::class, 'updatePassword'])->name('mentor.profile.updatePassword');
    Route::post('/mentor/update-status', [ProfileController::class, 'updateStatus'])->name('mentor.profile.updateStatus');
    // mentor reviews
    Route::get('/mentor/reviews', [ReviewController::class, 'index'])->name('mentor.reviews.index');

    //mentor courses
    Route::get('/mentor/courses', [CourseController::class, 'index'])->name('mentor.courses.index');
    Route::get('/mentor/courses/create', [CourseController::class, 'create'])->name('mentor.courses.create');
    Route::post('/mentor/courses/store', [CourseController::class, 'store'])->name('mentor.courses.store');
    Route::get('/mentor/courses/{course}', [CourseController::class, 'show'])->name('mentor.courses.show');
    Route::get('/mentor/courses/{course}/edit', [CourseController::class, 'edit'])->name('mentor.courses.edit');
    Route::post('/mentor/courses/{course}/update', [CourseController::class, 'update'])->name('mentor.courses.update');
    Route::delete('/mentor/courses/{course}/delete', [CourseController::class, 'destroy'])->name('mentor.courses.delete');

    // Time Slots (Session Bookings)
    Route::get('/mentor/time-slots', [TimeSlotController::class, 'index'])->name('mentor.time-slots.index');
    Route::get('/mentor/time-slots/create', [TimeSlotController::class, 'create'])->name('mentor.time-slots.create');
    Route::post('/mentor/time-slots', [TimeSlotController::class, 'store'])->name('mentor.time-slots.store');
    Route::get('/mentor/time-slots/{time_slot}', [TimeSlotController::class, 'show'])->name('mentor.time-slots.show');
    Route::get('/mentor/time-slots/{time_slot}/edit', [TimeSlotController::class, 'edit'])->name('mentor.time-slots.edit');
    Route::put('/mentor/time-slots/{time_slot}', [TimeSlotController::class, 'update'])->name('mentor.time-slots.update');
    Route::delete('/mentor/time-slots/{time_slot}', [TimeSlotController::class, 'destroy'])->name('mentor.time-slots.destroy');
});
