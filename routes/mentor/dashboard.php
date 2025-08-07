<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['mentor_auth'])->group(function () {
    Route::get('/mentor/dashboard', [App\Http\Controllers\backend\mentor\DashboardController::class, 'index'])->name('mentor.dashboard');
    // mentor profile
    Route::get('/mentor/profile', [App\Http\Controllers\backend\mentor\ProfileController::class, 'show'])->name('mentor.profile.show');
    Route::post('/mentor/profile-update', [App\Http\Controllers\backend\mentor\ProfileController::class, 'update'])->name('mentor.profile.update');
    Route::post('/mentor/password-update', [App\Http\Controllers\backend\mentor\ProfileController::class, 'updatePassword'])->name('mentor.profile.updatePassword');
    Route::post('/mentor/update-status', [App\Http\Controllers\backend\mentor\ProfileController::class, 'updateStatus'])->name('mentor.profile.updateStatus');
    // mentor reviews
    Route::get('/mentor/reviews', [App\Http\Controllers\backend\mentor\ReviewController::class, 'index'])->name('mentor.reviews.index');

    //mentor courses
    Route::get('/mentor/courses', [App\Http\Controllers\backend\mentor\CourseController::class, 'index'])->name('mentor.courses.index');
    Route::get('/mentor/courses/create', [App\Http\Controllers\backend\mentor\CourseController::class, 'create'])->name('mentor.courses.create');
    Route::post('/mentor/courses/store', [App\Http\Controllers\backend\mentor\CourseController::class, 'store'])->name('mentor.courses.store');
    Route::get('/mentor/courses/{course}', [App\Http\Controllers\backend\mentor\CourseController::class, 'show'])->name('mentor.courses.show');
    Route::get('/mentor/courses/{course}/edit', [App\Http\Controllers\backend\mentor\CourseController::class, 'edit'])->name('mentor.courses.edit');
    Route::post('/mentor/courses/{course}/update', [App\Http\Controllers\backend\mentor\CourseController::class, 'update'])->name('mentor.courses.update');
    Route::delete('/mentor/courses/{course}/delete', [App\Http\Controllers\backend\mentor\CourseController::class, 'destroy'])->name('mentor.courses.delete');

    // Time Slots (Session Bookings)
    Route::get('/mentor/time-slots', [App\Http\Controllers\backend\mentor\TimeSlotController::class, 'index'])->name('mentor.time-slots.index');
    Route::get('/mentor/time-slots/create', [App\Http\Controllers\backend\mentor\TimeSlotController::class, 'create'])->name('mentor.time-slots.create');
    Route::post('/mentor/time-slots', [App\Http\Controllers\backend\mentor\TimeSlotController::class, 'store'])->name('mentor.time-slots.store');
    Route::get('/mentor/time-slots/{time_slot}', [App\Http\Controllers\backend\mentor\TimeSlotController::class, 'show'])->name('mentor.time-slots.show');
    Route::get('/mentor/time-slots/{time_slot}/edit', [App\Http\Controllers\backend\mentor\TimeSlotController::class, 'edit'])->name('mentor.time-slots.edit');
    Route::put('/mentor/time-slots/{time_slot}', [App\Http\Controllers\backend\mentor\TimeSlotController::class, 'update'])->name('mentor.time-slots.update');
    Route::delete('/mentor/time-slots/{time_slot}', [App\Http\Controllers\backend\mentor\TimeSlotController::class, 'destroy'])->name('mentor.time-slots.destroy');
});
