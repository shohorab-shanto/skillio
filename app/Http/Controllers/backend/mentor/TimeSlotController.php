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
            if ($request->status == 'available') {
                $query->whereNull('user_id');
            } elseif ($request->status == 'booked') {
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
        $request->validate([
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
        foreach ($request->dates as $date) {
            $dateObj = \Carbon\Carbon::parse($date)->startOfDay();
            if ($dateObj->lt($today)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['dates' => 'Cannot create time slots for past dates.']);
            }
        }

        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        $createdCount = 0;

        // Loop through each selected date
        foreach ($request->dates as $date) {
            // Loop through each time slot
            foreach ($request->time_slots as $timeSlot) {
                $booking = SessionBooking::create([
                    'mentor_id' => $mentor->id,
                    'category_id' => $request->category_id,
                    'date' => $date,
                    'start_time' => $timeSlot['start_time'],
                    'end_time' => $timeSlot['end_time'],
                    'fee' => $request->fee,
                    'currency' => $request->currency,
                    'status' => 'active',
                ]);

                // Attach subcategory
                if ($request->sub_category_id) {
                    $booking->subCategories()->attach($request->sub_category_id);
                }

                $createdCount++;
            }
        }

        return redirect()->route('mentor.time-slots.index')
            ->with('success', "Successfully created {$createdCount} time slot" . ($createdCount > 1 ? 's' : '') . "!");
    }

    public function show(SessionBooking $timeSlot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        if ($timeSlot->mentor_id != $mentor->id) {
            abort(403);
        }

        $timeSlot->load(['category', 'subCategories', 'user']);
        
        return view('backend.mentor.time-slots.show', compact('timeSlot'));
    }

    public function edit(SessionBooking $timeSlot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        if ($timeSlot->mentor_id != $mentor->id) {
            abort(403);
        }

        $categories = Category::with('subCategories')->get();
        
        return view('backend.mentor.time-slots.edit', compact('timeSlot', 'categories'));
    }

    public function update(Request $request, SessionBooking $timeSlot)
    {
        $mentor = Mentor::where('user_id', Auth::id())->firstOrFail();
        
        if ($timeSlot->mentor_id != $mentor->id) {
            abort(403);
        }

        $timeSlot->update([
            'category_id' => $request->category_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'fee' => $request->fee,
            'currency' => $request->currency,
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
        
        if ($timeSlot->mentor_id != $mentor->id) {
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
