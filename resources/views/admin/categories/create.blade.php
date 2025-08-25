@extends('admin.layouts.backend')

@section('title', 'Create Category')

@section('header')
    Create Category
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Category</h1>
            <p class="text-gray-600 mt-1">Add a new category to organize courses and sessions</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categories.index') }}" 
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Categories
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Category Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="Enter category name"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">This name must be unique across all categories.</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Enter a description for this category (optional)"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Provide a brief description to help users understand this category.</p>
            </div>



            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Create Category
                </button>
            </div>
        </form>
    </div>

    <!-- Help Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-info text-sm text-blue-600"></i>
            </div>
            <div>
                <h3 class="text-sm font-medium text-blue-800 mb-2">Creating Categories</h3>
                <div class="text-sm text-blue-700 space-y-1">
                    <p>• <strong>Unique Names:</strong> Category names must be unique across the entire system.</p>
                    <p>• <strong>Organization:</strong> Categories help organize courses and sessions by topic.</p>
                    <p>• <strong>Sub-Categories:</strong> You can add sub-categories to further organize content.</p>
    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>


// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Please enter a category name.');
        document.getElementById('name').focus();
        return;
    }
});
</script>
@endpush
