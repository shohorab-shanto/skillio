<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\Course;
use App\Models\SessionBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSubCategoryController extends Controller
{
    public function index()
    {
        $subCategories = SubCategory::with(['category'])
            ->withCount(['courses', 'sessionBookings'])
            ->orderBy('name')
            ->paginate(15);
        
        return view('admin.sub-categories.index', compact('subCategories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.sub-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Check if name is unique within the same category
        $existingSubCategory = SubCategory::where('category_id', $request->category_id)
            ->where('name', $request->name)
            ->first();
        
        if ($existingSubCategory) {
            return back()->withErrors(['name' => 'A sub-category with this name already exists in the selected category.'])
                ->withInput();
        }

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sub-categories', 'public');
            $data['image'] = $imagePath;
        }

        SubCategory::create($data);

        return redirect()->route('admin.sub-categories.index')
            ->with('success', 'Sub-category created successfully!');
    }

    public function edit(SubCategory $subCategory)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.sub-categories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Check if name is unique within the same category (excluding current sub-category)
        $existingSubCategory = SubCategory::where('category_id', $request->category_id)
            ->where('name', $request->name)
            ->where('id', '!=', $subCategory->id)
            ->first();
        
        if ($existingSubCategory) {
            return back()->withErrors(['name' => 'A sub-category with this name already exists in the selected category.'])
                ->withInput();
        }

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($subCategory->image) {
                \Storage::disk('public')->delete($subCategory->image);
            }
            $imagePath = $request->file('image')->store('sub-categories', 'public');
            $data['image'] = $imagePath;
        }

        $subCategory->update($data);

        return redirect()->route('admin.sub-categories.index')
            ->with('success', 'Sub-category updated successfully!');
    }

    public function destroy(SubCategory $subCategory)
    {
        // Check if sub-category can be deleted
        $canDelete = $this->canDeleteSubCategory($subCategory);
        
        if (!$canDelete['can_delete']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete sub-category. ' . $canDelete['reason']
            ], 422);
        }

        // Delete associated image
        if ($subCategory->image) {
            \Storage::disk('public')->delete($subCategory->image);
        }

        $subCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub-category deleted successfully!'
        ]);
    }

    private function canDeleteSubCategory(SubCategory $subCategory)
    {
        // Check if any courses use this sub-category
        $coursesCount = $subCategory->courses()->count();
        if ($coursesCount > 0) {
            return [
                'can_delete' => false,
                'reason' => "This sub-category is used by {$coursesCount} course(s)."
            ];
        }

        // Check if any session bookings use this sub-category
        $sessionsCount = $subCategory->sessionBookings()->count();
        if ($sessionsCount > 0) {
            return [
                'can_delete' => false,
                'reason' => "This sub-category is used by {$sessionsCount} session(s)."
            ];
        }

        return ['can_delete' => true, 'reason' => ''];
    }

    public function getUsageStats(SubCategory $subCategory)
    {
        $stats = [
            'courses_count' => $subCategory->courses()->count(),
            'sessions_count' => $subCategory->sessionBookings()->count(),
        ];

        return response()->json($stats);
    }
}
