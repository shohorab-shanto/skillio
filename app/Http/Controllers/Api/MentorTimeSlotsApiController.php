<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionBooking;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorTimeSlotsApiController extends Controller
{
    /**
     * Display a listing of the mentor's time slots
     */
    public function index(Request $request)
    {
        $mentor = Mentor::where('user_id', Auth::id())->first();
        
        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }
        
        $query = SessionBooking::where('mentor_id', $mentor->id)
            ->with(['category', 'subCategories', 'user']);

        // Date range filtering
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Status filtering
        if ($request->filled('status')) {
            if ($request->status == 'available') {
                $query->whereNull('user_id');
            } elseif ($request->status == 'booked') {
                $query->whereNotNull('user_id');
            }
        }

        $perPage = $request->get('per_page', 12);
        $timeSlots = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);

        // Transform time slots for API response
        $timeSlots->getCollection()->transform(function ($timeSlot) {
            return [
                'id' => $timeSlot->id,
                'date' => $timeSlot->date ? $timeSlot->date->format('Y-m-d') : null,
                'start_time' => $timeSlot->start_time,
                'end_time' => $timeSlot->end_time,
                'formatted_time_slot' => $timeSlot->formatted_time_slot,
                'fee' => round($timeSlot->fee, 2),
                'currency' => $timeSlot->currency ?? 'USD',
                'status' => $timeSlot->status,
                'is_booked' => $timeSlot->user_id != null,
                'category' => [
                    'id' => $timeSlot->category->id,
                    'name' => $timeSlot->category->name,
                ],
                'sub_categories' => $timeSlot->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
                'user' => $timeSlot->user ? [
                    'id' => $timeSlot->user->id,
                    'name' => $timeSlot->user->name,
                    'photo' => $timeSlot->user->photo ? asset('storage/' . $timeSlot->user->photo) : null,
                ] : null,
                'created_at' => $timeSlot->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $timeSlot->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        // Get statistics
        $stats = [
            'total_slots' => SessionBooking::where('mentor_id', $mentor->id)->count(),
            'available_slots' => SessionBooking::where('mentor_id', $mentor->id)->whereNull('user_id')->count(),
            'booked_slots' => SessionBooking::where('mentor_id', $mentor->id)->whereNotNull('user_id')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'time_slots' => $timeSlots->items(),
                'pagination' => [
                    'current_page' => $timeSlots->currentPage(),
                    'last_page' => $timeSlots->lastPage(),
                    'per_page' => $timeSlots->perPage(),
                    'total' => $timeSlots->total(),
                ],
                'statistics' => $stats,
            ]
        ]);
    }

    /**
     * Store a newly created time slot in storage
     */
    public function store(Request $request)
    {
        $mentor = Mentor::where('user_id', Auth::id())->first();

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        $validated = $request->validate([
            'dates' => 'required|array|min:1',
            'dates.*' => 'required|date',
            'time_slots' => 'required|array|min:1',
            'time_slots.*.start_time' => 'required|date_format:H:i',
            'time_slots.*.end_time' => 'required|date_format:H:i|after:time_slots.*.start_time',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'currency' => 'required|in:USD,EUR',
            'fee' => 'required|numeric|min:0',
        ]);

        // Additional validation: Check if dates are not in the past
        $today = now()->startOfDay();
        foreach ($validated['dates'] as $date) {
            $dateObj = \Carbon\Carbon::parse($date)->startOfDay();
            if ($dateObj->lt($today)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create time slots for past dates.',
                    'errors' => [
                        'dates' => ['Cannot create time slots for past dates.']
                    ]
                ], 422);
            }
        }

        $createdSlots = [];
        $createdCount = 0;

        // Loop through each selected date
        foreach ($validated['dates'] as $date) {
            // Loop through each time slot
            foreach ($validated['time_slots'] as $timeSlot) {
                $booking = SessionBooking::create([
                    'mentor_id' => $mentor->id,
                    'category_id' => $validated['category_id'],
                    'date' => $date,
                    'start_time' => $timeSlot['start_time'],
                    'end_time' => $timeSlot['end_time'],
                    'fee' => $validated['fee'],
                    'currency' => $validated['currency'],
                    'status' => 'active',
                ]);

                // Attach subcategory
                if ($validated['sub_category_id']) {
                    $booking->subCategories()->attach($validated['sub_category_id']);
                }

                $booking->load(['category', 'subCategories']);
                
                $createdSlots[] = [
                    'id' => $booking->id,
                    'date' => $booking->date ? $booking->date->format('Y-m-d') : null,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'formatted_time_slot' => $booking->formatted_time_slot,
                    'fee' => round($booking->fee, 2),
                    'currency' => $booking->currency ?? 'USD',
                    'status' => $booking->status,
                    'is_booked' => false,
                    'category' => [
                        'id' => $booking->category->id,
                        'name' => $booking->category->name,
                    ],
                    'sub_categories' => $booking->subCategories->map(function($subCategory) {
                        return [
                            'id' => $subCategory->id,
                            'name' => $subCategory->name,
                        ];
                    }),
                    'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                ];

                $createdCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully created {$createdCount} time slot" . ($createdCount > 1 ? 's' : '') . "!",
            'data' => [
                'created_count' => $createdCount,
                'time_slots' => $createdSlots,
            ]
        ], 201);
    }

    /**
     * Display the specified time slot
     */
    public function show(SessionBooking $time_slot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->first();
        
        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. This endpoint is only available for mentors.'
            ], 403);
        }
        
        if ($time_slot->mentor_id != $mentor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. You can only view your own time slots.'
            ], 403);
        }

        $time_slot->load(['category', 'subCategories', 'user']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $time_slot->id,
                'date' => $time_slot->date ? $time_slot->date->format('Y-m-d') : null,
                'start_time' => $time_slot->start_time,
                'end_time' => $time_slot->end_time,
                'formatted_time_slot' => $time_slot->formatted_time_slot,
                'fee' => round($time_slot->fee, 2),
                'currency' => $time_slot->currency ?? 'USD',
                'status' => $time_slot->status,
                'is_booked' => $time_slot->user_id != null,
                'category' => [
                    'id' => $time_slot->category->id,
                    'name' => $time_slot->category->name,
                ],
                'sub_categories' => $time_slot->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
                'user' => $time_slot->user ? [
                    'id' => $time_slot->user->id,
                    'name' => $time_slot->user->name,
                    'photo' => $time_slot->user->photo ? asset('storage/' . $time_slot->user->photo) : null,
                    'email' => $time_slot->user->email,
                    'phone' => $time_slot->user->phone,
                ] : null,
                'created_at' => $time_slot->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $time_slot->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update the specified time slot in storage
     */
    public function update(Request $request, SessionBooking $time_slot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->first();
        
        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. This endpoint is only available for mentors.'
            ], 403);
        }
        
        if ($time_slot->mentor_id != $mentor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. You can only update your own time slots.'
            ], 403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'fee' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,EUR',
        ]);

        $time_slot->update([
            'category_id' => $validated['category_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'fee' => $validated['fee'],
            'currency' => $validated['currency'],
        ]);

        // Update single subcategory
        if ($validated['sub_category_id']) {
            $time_slot->subCategories()->sync([$validated['sub_category_id']]);
        } else {
            $time_slot->subCategories()->detach();
        }

        // Load relationships for response
        $time_slot->load(['category', 'subCategories', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Time slot updated successfully!',
            'data' => [
                'id' => $time_slot->id,
                'date' => $time_slot->date ? $time_slot->date->format('Y-m-d') : null,
                'start_time' => $time_slot->start_time,
                'end_time' => $time_slot->end_time,
                'formatted_time_slot' => $time_slot->formatted_time_slot,
                'fee' => round($time_slot->fee, 2),
                'currency' => $time_slot->currency ?? 'USD',
                'status' => $time_slot->status,
                'is_booked' => $time_slot->user_id != null,
                'category' => [
                    'id' => $time_slot->category->id,
                    'name' => $time_slot->category->name,
                ],
                'sub_categories' => $time_slot->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
                'user' => $time_slot->user ? [
                    'id' => $time_slot->user->id,
                    'name' => $time_slot->user->name,
                    'photo' => $time_slot->user->photo ? asset('storage/' . $time_slot->user->photo) : null,
                ] : null,
                'updated_at' => $time_slot->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Remove the specified time slot from storage
     */
    public function destroy(SessionBooking $time_slot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->first();
        
        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. This endpoint is only available for mentors.'
            ], 403);
        }
        
        if ($time_slot->mentor_id != $mentor->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. You can only delete your own time slots.'
            ], 403);
        }

        // Don't allow deletion if slot is booked
        if ($time_slot->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a booked time slot!'
            ], 400);
        }

        $time_slot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Time slot deleted successfully!'
        ]);
    }

    /**
     * Get categories and subcategories for time slot creation/editing
     */
    public function getFormData()
    {
        $categories = Category::with('subCategories')->get()->map(function($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'sub_categories' => $category->subCategories->map(function($subCategory) {
                    return [
                        'id' => $subCategory->id,
                        'name' => $subCategory->name,
                    ];
                }),
            ];
        });

        $currencies = [
            ['value' => 'USD', 'label' => 'USD ($)', 'symbol' => '$'],
            ['value' => 'EUR', 'label' => 'EUR (€)', 'symbol' => '€'],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'currencies' => $currencies,
            ]
        ]);
    }

    /**
     * Get time slot statistics
     */
    public function statistics()
    {
        $mentor = Mentor::where('user_id', Auth::id())->first();

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor profile not found.'
            ], 404);
        }

        $stats = [
            'total_slots' => SessionBooking::where('mentor_id', $mentor->id)->count(),
            'available_slots' => SessionBooking::where('mentor_id', $mentor->id)->whereNull('user_id')->count(),
            'booked_slots' => SessionBooking::where('mentor_id', $mentor->id)->whereNotNull('user_id')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
