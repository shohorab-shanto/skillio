<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    /**
     * Display a listing of sliders
     */
    public function index()
    {
        $sliders = Slider::orderBy('order', 'asc')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new slider
     */
    public function create()
    {
        $maxOrder = Slider::max('order') ?? 0;
        return view('admin.sliders.create', compact('maxOrder'));
    }

    /**
     * Store a newly created slider in storage
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('sliders', 'public');
            }

            Slider::create([
                'image' => $imagePath,
                'order' => $request->order,
                'status' => $request->status ?? 'active',
            ]);

            return redirect()->route('admin.sliders.index')
                ->with('success', 'Slider created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create slider: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified slider
     */
    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified slider in storage
     */
    public function update(Request $request, Slider $slider)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'order' => $request->order,
                'status' => $request->status ?? 'active',
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($slider->image) {
                    Storage::disk('public')->delete($slider->image);
                }
                
                $data['image'] = $request->file('image')->store('sliders', 'public');
            }

            $slider->update($data);

            return redirect()->route('admin.sliders.index')
                ->with('success', 'Slider updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update slider: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified slider from storage
     */
    public function destroy(Slider $slider)
    {
        try {
            // Delete image from storage
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }

            $slider->delete();

            return redirect()->route('admin.sliders.index')
                ->with('success', 'Slider deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete slider: ' . $e->getMessage());
        }
    }

    /**
     * Update the order of sliders
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sliders' => 'required|array',
            'sliders.*.id' => 'required|exists:sliders,id',
            'sliders.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            foreach ($request->sliders as $sliderData) {
                Slider::where('id', $sliderData['id'])
                    ->update(['order' => $sliderData['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Slider order updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update slider order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle slider status
     */
    public function toggleStatus(Slider $slider)
    {
        try {
            $slider->status = $slider->status == 'active' ? 'inactive' : 'active';
            $slider->save();

            return redirect()->back()
                ->with('success', 'Slider status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}
