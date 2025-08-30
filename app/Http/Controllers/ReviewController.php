<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
            'course_id' => 'nullable|exists:courses,id',
            'mentor_id' => 'nullable|exists:users,id'
        ]);

        // Ensure only one of course_id or mentor_id is provided
        if (!$request->course_id && !$request->mentor_id) {
            return response()->json(['message' => 'Either course_id or mentor_id is required'], 400);
        }

        if ($request->course_id && $request->mentor_id) {
            return response()->json(['message' => 'Cannot review both course and mentor at the same time'], 400);
        }

        $user = Auth::user();

        // Check if user can review this course/mentor
        if ($request->course_id) {
            $course = Course::findOrFail($request->course_id);
            
            // Check if user is enrolled in this course
            $enrollment = $user->enrollments()
                ->where('enrollable_type', Course::class)
                ->where('enrollable_id', $course->id)
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->first();

            if (!$enrollment) {
                return response()->json(['message' => 'You can only review courses you are enrolled in'], 403);
            }

            // Check if user already reviewed this course
            $existingReview = Review::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingReview) {
                return response()->json(['message' => 'You have already reviewed this course'], 400);
            }

            $review = Review::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'mentor_id' => null,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            return response()->json([
                'message' => 'Review submitted successfully!',
                'review' => $review
            ], 201);

        } else {
            $mentor = User::findOrFail($request->mentor_id);
            
            // Check if user has booked sessions with this mentor
            $hasBookedSessions = $user->enrollments()
                ->where('enrollable_type', 'App\Models\SessionBooking')
                ->whereHas('enrollable', function($query) use ($mentor) {
                    $query->where('mentor_id', $mentor->id);
                })
                ->whereIn('enrollment_status', ['active', 'completed'])
                ->exists();

            if (!$hasBookedSessions) {
                return response()->json(['message' => 'You can only review mentors you have booked sessions with'], 403);
            }

            // Check if user already reviewed this mentor
            $existingReview = Review::where('user_id', $user->id)
                ->where('mentor_id', $mentor->id)
                ->first();

            if ($existingReview) {
                return response()->json(['message' => 'You have already reviewed this mentor'], 400);
            }

            $review = Review::create([
                'user_id' => $user->id,
                'course_id' => null,
                'mentor_id' => $mentor->id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            return response()->json([
                'message' => 'Review submitted successfully!',
                'review' => $review
            ], 201);
        }
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, Review $review)
    {
        $user = Auth::user();

        // Check if user owns this review
        if ($review->user_id != $user->id) {
            return response()->json(['message' => 'You can only edit your own reviews'], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000'
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'Review updated successfully!',
            'review' => $review
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();

        // Check if user owns this review
        if ($review->user_id != $user->id) {
            return redirect()->back()->with('error', 'You can only delete your own reviews');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully!');
    }
}
