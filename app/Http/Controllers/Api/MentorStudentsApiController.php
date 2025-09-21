<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\UserEnrollment;
use App\Models\SessionBooking;
use App\Models\Conversation;
use Carbon\Carbon;

class MentorStudentsApiController extends Controller
{
    /**
     * Get mentor's students/users list
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        // Get student list with search and filter
        $studentQuery = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->with(['user', 'enrollable', 'enrollable.category', 'enrollable.subCategories']);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $studentQuery->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                })->orWhere(function($subQuery) use ($search) {
                    // For courses, search in title
                    $subQuery->where('enrollable_type', Course::class)
                            ->whereRaw('enrollable_id IN (SELECT id FROM courses WHERE title LIKE ?)', ["%{$search}%"]);
                })->orWhere(function($subQuery) use ($search) {
                    // For session bookings, search in category name
                    $subQuery->where('enrollable_type', SessionBooking::class)
                            ->whereRaw('enrollable_id IN (SELECT sb.id FROM session_bookings sb INNER JOIN categories c ON sb.category_id = c.id WHERE c.name LIKE ?)', ["%{$search}%"]);
                });
            });
        }

        $perPage = $request->get('per_page', 10);
        $students = $studentQuery->orderBy('created_at', 'desc')->paginate($perPage);

        // Calculate duration left for each enrollment and add conversation data
        $students->getCollection()->transform(function ($enrollment) use ($mentor) {
            if ($enrollment->enrollable_type == Course::class) {
                $course = $enrollment->enrollable;
                if ($course->end_date) {
                    $now = Carbon::now();
                    $endDate = Carbon::parse($course->end_date);
                    
                    if ($endDate->gt($now)) {
                        $diff = $now->diff($endDate);
                        $durationParts = [];
                        
                        if ($diff->m > 0) {
                            $durationParts[] = $diff->m . ' month' . ($diff->m > 1 ? 's' : '');
                        }
                        if ($diff->d > 0) {
                            $durationParts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
                        }
                        if ($diff->h > 0) {
                            $durationParts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                        }
                        if ($diff->i > 0) {
                            $durationParts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                        }
                        
                        $enrollment->duration_left = implode(' ', $durationParts);
                        $enrollment->is_active = true;
                    } else {
                        $enrollment->duration_left = 'Expired';
                        $enrollment->is_active = false;
                    }
                } else {
                    $enrollment->duration_left = 'No end date';
                    $enrollment->is_active = true;
                }
            } else {
                // Session booking
                $session = $enrollment->enrollable;
                if ($session->start_time && $session->end_time) {
                    $now = Carbon::now();
                    $startTime = Carbon::parse($session->start_time);
                    $endTime = Carbon::parse($session->end_time);
                    
                    if ($now->between($startTime, $endTime)) {
                        $enrollment->duration_left = 'Active now';
                        $enrollment->is_active = true;
                    } elseif ($endTime->gt($now)) {
                        $diff = $now->diff($endTime);
                        $durationParts = [];
                        
                        if ($diff->d > 0) {
                            $durationParts[] = $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
                        }
                        if ($diff->h > 0) {
                            $durationParts[] = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
                        }
                        if ($diff->i > 0) {
                            $durationParts[] = $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
                        }
                        
                        $enrollment->duration_left = implode(' ', $durationParts);
                        $enrollment->is_active = true;
                    } else {
                        $enrollment->duration_left = 'Completed';
                        $enrollment->is_active = false;
                    }
                } else {
                    $enrollment->duration_left = 'No time set';
                    $enrollment->is_active = true;
                }
            }

            // Get or create conversation
            $student = $enrollment->user;
            $conversation = Conversation::where('mentor_id', $mentor->id)
                ->where('user_id', $student->id)
                ->first();
            
            if (!$conversation) {
                $conversation = Conversation::create([
                    'mentor_id' => $mentor->id,
                    'user_id' => $student->id,
                    'last_message_at' => now(),
                ]);
            }

            // Transform enrollment for API response
            $service = $enrollment->enrollable;
            
            return [
                'id' => $enrollment->id,
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'address' => $student->address,
                    'photo' => $student->photo ? asset('storage/' . $student->photo) : null,
                    'status' => $student->status,
                    'created_at' => $student->created_at->format('Y-m-d H:i:s'),
                ],
                'service' => [
                    'id' => $service->id,
                    'title' => $enrollment->enrollable_type == Course::class ? $service->title : 'Session',
                    'type' => $enrollment->enrollable_type == Course::class ? 'Course' : 'Session',
                    'category' => $service->category ? [
                        'id' => $service->category->id,
                        'name' => $service->category->name,
                    ] : null,
                    'sub_categories' => $service->subCategories ? $service->subCategories->map(function($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }) : [],
                ],
                'enrollment' => [
                    'enrollment_date' => $enrollment->created_at->format('Y-m-d'),
                    'enrollment_status' => $enrollment->enrollment_status,
                    'duration_left' => $enrollment->duration_left,
                    'is_active' => $enrollment->is_active,
                    'status' => $enrollment->is_active ? 'active' : 'inactive',
                    'amount' => round($enrollment->amount, 2),
                    'currency' => $enrollment->currency,
                    'payment_status' => $enrollment->payment_status,
                ],
                'conversation' => [
                    'id' => $conversation->id,
                    'unique_code' => $conversation->unique_code,
                    'last_message_at' => $conversation->last_message_at ? $conversation->last_message_at->format('Y-m-d H:i:s') : null,
                ],
            ];
        });

        // Apply status filter after calculating is_active
        if ($request->filled('status') && $request->status != 'all') {
            $filteredCollection = $students->getCollection()->filter(function ($enrollment) use ($request) {
                if ($request->status == 'active') {
                    return $enrollment['enrollment']['is_active'] == true;
                } elseif ($request->status == 'inactive') {
                    return $enrollment['enrollment']['is_active'] == false;
                }
                return true;
            });
            
            // Update the collection
            $students->setCollection($filteredCollection->values());
        }

        // Get student statistics
        $totalStudents = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->distinct('user_id')->count();

        $activeEnrollments = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('enrollment_status', 'active')->count();

        $completedEnrollments = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('enrollment_status', 'completed')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $students->items(),
                'pagination' => [
                    'current_page' => $students->currentPage(),
                    'last_page' => $students->lastPage(),
                    'per_page' => $students->perPage(),
                    'total' => $students->total(),
                ],
                'statistics' => [
                    'total_students' => $totalStudents,
                    'total_enrollments' => $students->total(),
                    'active_enrollments' => $activeEnrollments,
                    'completed_enrollments' => $completedEnrollments,
                ],
                'filters' => [
                    'search' => $request->get('search', ''),
                    'status' => $request->get('status', 'all'),
                ],
            ]
        ]);
    }

    /**
     * Get specific student details for mentor
     */
    public function show(Request $request, $studentId)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        // Get student's enrollments with this mentor
        $enrollments = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('user_id', $studentId)
          ->with(['user', 'enrollable', 'enrollable.category', 'enrollable.subCategories', 'paymentTransaction'])
          ->get();

        if ($enrollments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found or not enrolled with this mentor.'
            ], 404);
        }

        $student = $enrollments->first()->user;

        // Get or create conversation
        $conversation = Conversation::where('mentor_id', $mentor->id)
            ->where('user_id', $student->id)
            ->first();
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'mentor_id' => $mentor->id,
                'user_id' => $student->id,
                'last_message_at' => now(),
            ]);
        }

        // Transform enrollments
        $enrollmentData = $enrollments->map(function($enrollment) {
            $service = $enrollment->enrollable;
            
            return [
                'id' => $enrollment->id,
                'service' => [
                    'id' => $service->id,
                    'title' => $enrollment->enrollable_type == Course::class ? $service->title : 'Session',
                    'type' => $enrollment->enrollable_type == Course::class ? 'Course' : 'Session',
                    'category' => $service->category ? [
                        'id' => $service->category->id,
                        'name' => $service->category->name,
                    ] : null,
                ],
                'enrollment' => [
                    'status' => $enrollment->enrollment_status,
                    'enrolled_at' => $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : null,
                    'amount' => round($enrollment->amount, 2),
                    'currency' => $enrollment->currency,
                    'payment_status' => $enrollment->payment_status,
                ],
                'payment_transaction' => $enrollment->paymentTransaction ? [
                    'id' => $enrollment->paymentTransaction->id,
                    'transaction_id' => $enrollment->paymentTransaction->transaction_id,
                    'gross_amount' => round($enrollment->paymentTransaction->gross_amount, 2),
                    'mentor_amount' => round($enrollment->paymentTransaction->mentor_amount, 2),
                    'transaction_status' => $enrollment->paymentTransaction->transaction_status,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'address' => $student->address,
                    'photo' => $student->photo ? asset('storage/' . $student->photo) : null,
                    'status' => $student->status,
                    'created_at' => $student->created_at->format('Y-m-d H:i:s'),
                ],
                'enrollments' => $enrollmentData,
                'conversation' => [
                    'id' => $conversation->id,
                    'unique_code' => $conversation->unique_code,
                    'last_message_at' => $conversation->last_message_at ? $conversation->last_message_at->format('Y-m-d H:i:s') : null,
                ],
                'statistics' => [
                    'total_enrollments' => $enrollments->count(),
                    'active_enrollments' => $enrollments->where('enrollment_status', 'active')->count(),
                    'completed_enrollments' => $enrollments->where('enrollment_status', 'completed')->count(),
                    'total_spent' => $enrollments->sum('amount'),
                ],
            ]
        ]);
    }

    /**
     * Get student statistics for mentor
     */
    public function statistics(Request $request)
    {
        $user = Auth::user();
        $mentor = $user->mentor;

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        // Get comprehensive student statistics
        $totalStudents = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->distinct('user_id')->count();

        $totalEnrollments = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->count();

        $activeEnrollments = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('enrollment_status', 'active')->count();

        $completedEnrollments = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('enrollment_status', 'completed')->count();

        // Get enrollment breakdown by type
        $courseEnrollments = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('enrollable_type', Course::class)->count();

        $sessionEnrollments = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->where('enrollable_type', SessionBooking::class)->count();

        // Get new students by month (last 6 months)
        $newStudentsByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $newStudents = UserEnrollment::whereHas('enrollable', function($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })->whereMonth('created_at', $date->month)
              ->whereYear('created_at', $date->year)
              ->distinct('user_id')
              ->count();
            
            $newStudentsByMonth[] = [
                'month' => $date->format('M Y'),
                'new_students' => $newStudents
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_students' => $totalStudents,
                    'total_enrollments' => $totalEnrollments,
                    'active_enrollments' => $activeEnrollments,
                    'completed_enrollments' => $completedEnrollments,
                ],
                'enrollment_breakdown' => [
                    'course_enrollments' => $courseEnrollments,
                    'session_enrollments' => $sessionEnrollments,
                ],
                'new_students_by_month' => $newStudentsByMonth,
                'retention_rate' => $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 2) : 0,
            ]
        ]);
    }
}
