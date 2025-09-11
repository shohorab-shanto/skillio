<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewApiController extends Controller
{
    /**
     * Create a new review for a course or mentor
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:10|max:1000',
                'course_id' => 'nullable|exists:courses,id',
                'mentor_id' => 'nullable|exists:mentors,id'
            ]);

            // Ensure only one of course_id or mentor_id is provided
            if (!$request->course_id && !$request->mentor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either course_id or mentor_id is required',
                    'errors' => ['target' => ['Either course_id or mentor_id is required']]
                ], 400);
            }

            if ($request->course_id && $request->mentor_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot review both course and mentor at the same time',
                    'errors' => ['target' => ['Cannot review both course and mentor at the same time']]
                ], 400);
            }

            $user = Auth::user();

            if ($request->course_id) {
                return $this->createCourseReview($request, $user);
            } else {
                return $this->createMentorReview($request, $user);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a review for a course
     */
    private function createCourseReview(Request $request, User $user)
    {
        $course = Course::findOrFail($request->course_id);
        
        // Check if user is enrolled in this course
        $enrollment = $user->enrollments()
            ->where('enrollable_type', Course::class)
            ->where('enrollable_id', $course->id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review courses you are enrolled in',
                'errors' => ['enrollment' => ['You must be enrolled in this course to review it']]
            ], 403);
        }

        // Check if user already reviewed this course
        $existingReview = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this course',
                'errors' => ['review' => ['You have already reviewed this course']]
            ], 400);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'mentor_id' => null,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        // Load relationships for response
        $review->load(['user', 'course.mentor.user']);

        return response()->json([
            'success' => true,
            'message' => 'Course review submitted successfully',
            'data' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user_name' => $review->user->name,
                'user_photo' => $review->user->photo,
                'course_title' => $review->course->title,
                'mentor_name' => $review->course->mentor->user->name,
                'created_at' => $review->created_at,
                'updated_at' => $review->updated_at
            ]
        ], 201);
    }

    /**
     * Create a review for a mentor
     */
    private function createMentorReview(Request $request, User $user)
    {
        $mentor = Mentor::findOrFail($request->mentor_id);
        
        // Check if user has booked sessions with this mentor
        $hasBookedSessions = $user->enrollments()
            ->where('enrollable_type', 'App\Models\SessionBooking')
            ->whereHas('enrollable', function($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->exists();

        if (!$hasBookedSessions) {
            return response()->json([
                'success' => false,
                'message' => 'You can only review mentors you have booked sessions with',
                'errors' => ['enrollment' => ['You must have booked sessions with this mentor to review them']]
            ], 403);
        }

        // Check if user already reviewed this mentor
        $existingReview = Review::where('user_id', $user->id)
            ->where('mentor_id', $mentor->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this mentor',
                'errors' => ['review' => ['You have already reviewed this mentor']]
            ], 400);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'course_id' => null,
            'mentor_id' => $mentor->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        // Load relationships for response
        $review->load(['user', 'mentor.user']);

        return response()->json([
            'success' => true,
            'message' => 'Mentor review submitted successfully',
            'data' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user_name' => $review->user->name,
                'user_photo' => $review->user->photo,
                'mentor_name' => $review->mentor->user->name,
                'created_at' => $review->created_at,
                'updated_at' => $review->updated_at
            ]
        ], 201);
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, Review $review)
    {
        try {
            $user = Auth::user();

            // Check if user owns this review
            if ($review->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only edit your own reviews',
                    'errors' => ['permission' => ['You can only edit your own reviews']]
                ], 403);
            }

            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|min:10|max:1000'
            ]);

            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            // Load relationships for response
            $review->load(['user', 'course.mentor.user', 'mentor.user']);

            $responseData = [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user_name' => $review->user->name,
                'user_photo' => $review->user->photo,
                'created_at' => $review->created_at,
                'updated_at' => $review->updated_at
            ];

            if ($review->course_id) {
                $responseData['course_title'] = $review->course->title;
                $responseData['mentor_name'] = $review->course->mentor->user->name;
            } else {
                $responseData['mentor_name'] = $review->mentor->user->name;
            }

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully',
                'data' => $responseData
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review)
    {
        try {
            $user = Auth::user();

            // Check if user owns this review
            if ($review->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own reviews',
                    'errors' => ['permission' => ['You can only delete your own reviews']]
                ], 403);
            }

            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's reviews
     */
    public function getUserReviews(Request $request)
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 10);
            $type = $request->get('type', 'all'); // all, course, mentor

            $query = Review::where('user_id', $user->id)
                ->with(['user', 'course.mentor.user', 'mentor.user']);

            if ($type === 'course') {
                $query->whereNotNull('course_id');
            } elseif ($type === 'mentor') {
                $query->whereNotNull('mentor_id');
            }

            $reviews = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $reviews->getCollection()->transform(function ($review) {
                $data = [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'user_name' => $review->user->name,
                    'user_photo' => $review->user->photo,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at
                ];

                if ($review->course_id) {
                    $data['type'] = 'course';
                    $data['course_title'] = $review->course->title;
                    $data['mentor_name'] = $review->course->mentor->user->name;
                } else {
                    $data['type'] = 'mentor';
                    $data['mentor_name'] = $review->mentor->user->name;
                }

                return $data;
            });

            return response()->json([
                'success' => true,
                'message' => 'User reviews retrieved successfully',
                'data' => $reviews->items(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                    'has_more_pages' => $reviews->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific review
     */
    public function show(Review $review)
    {
        try {
            $review->load(['user', 'course.mentor.user', 'mentor.user']);

            $data = [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user_name' => $review->user->name,
                'user_photo' => $review->user->photo,
                'created_at' => $review->created_at,
                'updated_at' => $review->updated_at
            ];

            if ($review->course_id) {
                $data['type'] = 'course';
                $data['course_title'] = $review->course->title;
                $data['mentor_name'] = $review->course->mentor->user->name;
            } else {
                $data['type'] = 'mentor';
                $data['mentor_name'] = $review->mentor->user->name;
            }

            return response()->json([
                'success' => true,
                'message' => 'Review retrieved successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can review a course or mentor
     */
    public function canReview(Request $request)
    {
        try {
            $user = Auth::user();
            $courseId = $request->get('course_id');
            $mentorId = $request->get('mentor_id');

            if (!$courseId && !$mentorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either course_id or mentor_id is required',
                    'errors' => ['target' => ['Either course_id or mentor_id is required']]
                ], 400);
            }

            if ($courseId && $mentorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot check both course and mentor at the same time',
                    'errors' => ['target' => ['Cannot check both course and mentor at the same time']]
                ], 400);
            }

            if ($courseId) {
                return $this->checkCourseReviewEligibility($user, $courseId);
            } else {
                return $this->checkMentorReviewEligibility($user, $mentorId);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking review eligibility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can review a course
     */
    private function checkCourseReviewEligibility(User $user, $courseId)
    {
        $course = Course::findOrFail($courseId);
        
        // Check if user is enrolled in this course
        $enrollment = $user->enrollments()
            ->where('enrollable_type', Course::class)
            ->where('enrollable_id', $course->id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => true,
                'message' => 'Review eligibility checked',
                'data' => [
                    'can_review' => false,
                    'reason' => 'You must be enrolled in this course to review it',
                    'course_title' => $course->title
                ]
            ]);
        }

        // Check if user already reviewed this course
        $existingReview = Review::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => true,
                'message' => 'Review eligibility checked',
                'data' => [
                    'can_review' => false,
                    'reason' => 'You have already reviewed this course',
                    'course_title' => $course->title,
                    'existing_review' => [
                        'id' => $existingReview->id,
                        'rating' => $existingReview->rating,
                        'comment' => $existingReview->comment,
                        'created_at' => $existingReview->created_at
                    ]
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Review eligibility checked',
            'data' => [
                'can_review' => true,
                'reason' => 'You are eligible to review this course',
                'course_title' => $course->title
            ]
        ]);
    }

    /**
     * Check if user can review a mentor
     */
    private function checkMentorReviewEligibility(User $user, $mentorId)
    {
        $mentor = Mentor::findOrFail($mentorId);
        
        // Check if user has booked sessions with this mentor
        $hasBookedSessions = $user->enrollments()
            ->where('enrollable_type', 'App\Models\SessionBooking')
            ->whereHas('enrollable', function($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->exists();

        if (!$hasBookedSessions) {
            return response()->json([
                'success' => true,
                'message' => 'Review eligibility checked',
                'data' => [
                    'can_review' => false,
                    'reason' => 'You must have booked sessions with this mentor to review them',
                    'mentor_name' => $mentor->user->name
                ]
            ]);
        }

        // Check if user already reviewed this mentor
        $existingReview = Review::where('user_id', $user->id)
            ->where('mentor_id', $mentor->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => true,
                'message' => 'Review eligibility checked',
                'data' => [
                    'can_review' => false,
                    'reason' => 'You have already reviewed this mentor',
                    'mentor_name' => $mentor->user->name,
                    'existing_review' => [
                        'id' => $existingReview->id,
                        'rating' => $existingReview->rating,
                        'comment' => $existingReview->comment,
                        'created_at' => $existingReview->created_at
                    ]
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Review eligibility checked',
            'data' => [
                'can_review' => true,
                'reason' => 'You are eligible to review this mentor',
                'mentor_name' => $mentor->user->name
            ]
        ]);
    }
}
