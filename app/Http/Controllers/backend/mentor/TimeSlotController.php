<?php

namespace App\Http\Controllers\backend\mentor;

use App\Http\Controllers\Controller;
use App\Models\SessionBooking;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeSlotController extends Controller
{
    public function index(Request $request)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
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
            if ($request->status === 'available') {
                $query->whereNull('user_id');
            } elseif ($request->status === 'booked') {
                $query->whereNotNull('user_id');
            }
        }

        $timeSlots = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(12);

        return view('backend.mentor.time-slots.index', compact('timeSlots', 'request'));
    }

    public function create()
    {
        $categories = Category::with('subCategories')->get();
        
        return view('backend.mentor.time-slots.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();

        $timeSlot = SessionBooking::create([
            'mentor_id' => $mentor->id,
            'category_id' => $request->category_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'fee' => $request->fee,
            'status' => 'active',
        ]);

        // Attach single subcategory (even though model supports multiple)
        if ($request->sub_category_id) {
            $timeSlot->subCategories()->attach($request->sub_category_id);
        }

        return redirect()->route('mentor.time-slots.index')->with('success', 'Time slot created successfully!');
    }

    public function show(SessionBooking $timeSlot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        if ($timeSlot->mentor_id !== $mentor->id) {
            abort(403);
        }

        $timeSlot->load(['category', 'subCategories', 'user']);
        
        return view('backend.mentor.time-slots.show', compact('timeSlot'));
    }

    public function edit(SessionBooking $timeSlot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        if ($timeSlot->mentor_id !== $mentor->id) {
            abort(403);
        }

        $categories = Category::with('subCategories')->get();
        
        return view('backend.mentor.time-slots.edit', compact('timeSlot', 'categories'));
    }

    public function update(Request $request, SessionBooking $timeSlot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        if ($timeSlot->mentor_id !== $mentor->id) {
            abort(403);
        }

        $timeSlot->update([
            'category_id' => $request->category_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'fee' => $request->fee,
        ]);

        // Update single subcategory
        if ($request->sub_category_id) {
            $timeSlot->subCategories()->sync([$request->sub_category_id]);
        } else {
            $timeSlot->subCategories()->detach();
        }

        return redirect()->route('mentor.time-slots.index')->with('success', 'Time slot updated successfully!');
    }

    public function destroy(SessionBooking $timeSlot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        if ($timeSlot->mentor_id !== $mentor->id) {
            abort(403);
        }

        // Don't allow deletion if slot is booked
        if ($timeSlot->user_id) {
            return redirect()->back()->with('error', 'Cannot delete a booked time slot!');
        }

        $timeSlot->delete();

        return redirect()->route('mentor.time-slots.index')->with('success', 'Time slot deleted successfully!');
    }
}
