<?php

namespace App\Http\Controllers\Backend\Mentor;

use App\Http\Controllers\Controller;
use App\Models\SessionBooking;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        $query = SessionBooking::with(['category', 'subCategories', 'user'])
            ->where('mentor_id', $mentor->id)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc');

        // Filter by date range
        if ($request->date_from && $request->date_to) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        } elseif ($request->date_from) {
            $query->where('date', '>=', $request->date_from);
        } elseif ($request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->status) {
            // Map frontend filter values to actual database values
            if ($request->status === 'available') {
                $query->where('status', 'active');
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $timeSlots = $query->paginate(12);

        // Get statistics
        $stats = [
            'total_slots' => SessionBooking::where('mentor_id', $mentor->id)->count(),
            'active_slots' => SessionBooking::where('mentor_id', $mentor->id)->where('status', 'active')->count(),
            'booked_slots' => SessionBooking::where('mentor_id', $mentor->id)->whereNotNull('user_id')->count(),
            'completed_slots' => SessionBooking::where('mentor_id', $mentor->id)->where('status', 'completed')->count(),
        ];

        return view('backend.mentor.time-slots.index', compact('timeSlots', 'stats', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::with('subCategories')->get();
        return view('backend.mentor.time-slots.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        $timeSlot = SessionBooking::create([
            'mentor_id' => $mentor->id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'fee' => $request->fee,
            'status' => 'active',
            'payment_status' => 'unpaid',
        ]);

        // Attach subcategories
        if ($request->sub_category_ids) {
            $timeSlot->subCategories()->attach($request->sub_category_ids);
        }

        return redirect()->route('mentor.time-slots.index')
            ->with('success', 'Time slot created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SessionBooking $timeSlot)
    {
        $this->authorize('view', $timeSlot);
        
        $timeSlot->load(['category', 'subCategories', 'user', 'mentor.user']);
        
        return view('backend.mentor.time-slots.show', compact('timeSlot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SessionBooking $timeSlot)
    {
        $this->authorize('update', $timeSlot);
        
        // Check if slot status is active (only active slots can be edited)
        if ($timeSlot->status !== 'active') {
            return redirect()->route('mentor.time-slots.index')
                ->with('error', 'Only active time slots can be edited!');
        }
        
        $categories = Category::with('subCategories')->get();
        $timeSlot->load(['category', 'subCategories']);
        
        return view('backend.mentor.time-slots.edit', compact('timeSlot', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SessionBooking $timeSlot)
    {
        $this->authorize('update', $timeSlot);
        
        // Check if slot status is active (only active slots can be updated)
        if ($timeSlot->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Only active time slots can be updated!');
        }
        
        $timeSlot->update([
            'category_id' => $request->category_id,
            'type' => $request->type,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'fee' => $request->fee,
        ]);

        // Sync subcategories
        if ($request->sub_category_ids) {
            $timeSlot->subCategories()->sync($request->sub_category_ids);
        } else {
            $timeSlot->subCategories()->detach();
        }

        return redirect()->route('mentor.time-slots.index')
            ->with('success', 'Time slot updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SessionBooking $timeSlot)
    {
        $this->authorize('delete', $timeSlot);
        
        // Check if slot is booked
        if ($timeSlot->user_id) {
            return redirect()->back()
                ->with('error', 'Cannot delete a booked time slot!');
        }

        // Check if slot status is active (only active slots can be deleted)
        if ($timeSlot->status !== 'active') {
            return redirect()->back()
                ->with('error', 'Only active time slots can be deleted!');
        }

        $timeSlot->subCategories()->detach();
        $timeSlot->delete();

        return redirect()->route('mentor.time-slots.index')
            ->with('success', 'Time slot deleted successfully!');
    }

    /**
     * Get subcategories for a category (AJAX)
     */
    public function getSubCategories(Category $category)
    {
        return response()->json($category->subCategories);
    }
}
