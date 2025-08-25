<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\SessionBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['subCategories', 'courses', 'sessionBookings'])
            ->orderBy('name')
            ->paginate(15);
        
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                \Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // Check if category can be deleted
        $canDelete = $this->canDeleteCategory($category);
        
        if (!$canDelete['can_delete']) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category. ' . $canDelete['reason']
            ], 422);
        }

        // Delete associated image
        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
        ]);
    }

    private function canDeleteCategory(Category $category)
    {
        // Check if any courses use this category
        $coursesCount = Course::where('category_id', $category->id)->count();
        if ($coursesCount > 0) {
            return [
                'can_delete' => false,
                'reason' => "This category is used by {$coursesCount} course(s)."
            ];
        }

        // Check if any session bookings use this category
        $sessionsCount = SessionBooking::where('category_id', $category->id)->count();
        if ($sessionsCount > 0) {
            return [
                'can_delete' => false,
                'reason' => "This category is used by {$sessionsCount} session(s)."
            ];
        }

        // Check if any sub-categories exist
        $subCategoriesCount = $category->subCategories()->count();
        if ($subCategoriesCount > 0) {
            return [
                'can_delete' => false,
                'reason' => "This category has {$subCategoriesCount} sub-category(ies)."
            ];
        }

        return ['can_delete' => true, 'reason' => ''];
    }

    public function getUsageStats(Category $category)
    {
        $stats = [
            'courses_count' => Course::where('category_id', $category->id)->count(),
            'sessions_count' => SessionBooking::where('category_id', $category->id)->count(),
            'sub_categories_count' => $category->subCategories()->count(),
        ];

        return response()->json($stats);
    }
}
