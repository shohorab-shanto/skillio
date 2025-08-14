@extends('backend.layouts.app')

@section('title', 'Create Time Slot')

@push('styles')
<style>
/* Force remove all dropdown arrows across all browsers */
select.custom-dropdown {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-image: none !important;
    background: white !important;
}

/* Extra specificity for stubborn browsers */
select.custom-dropdown::-ms-expand {
    display: none;
}

/* Firefox specific */
select.custom-dropdown:-moz-focusring {
    color: transparent;
    text-shadow: 0 0 0 #000;
}
</style>
@endpush
@section('pageTitle', 'Create New Course')
@section('backUrl', route('mentor.dashboard'))

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('mentor.time-slots.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Time Slot</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Main Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('mentor.time-slots.store') }}" class="space-y-8">
            @csrf
            <!-- Date Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                    <input type="date" 
                           name="date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           value="{{ old('date') }}" required>
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" 
                               name="fee"
                               step="0.01"
                               min="0"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="0.00" 
                               value="{{ old('fee') }}" required>
                    </div>
                    @error('fee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Category Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <div class="relative">
                        <select name="category_id" 
                                id="category_id"
                                class="custom-dropdown w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900 cursor-pointer hover:border-gray-400 transition-colors"
                                required onchange="updateSubCategories()">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sub Category *</label>
                    <div class="relative">
                        <select name="sub_category_id" 
                                id="sub_category_id"
                                class="custom-dropdown w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900 cursor-pointer hover:border-gray-400 transition-colors"
                                required>
                            <option value="">Select a sub category</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                    @error('sub_category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Time Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                    <input type="time" 
                           name="start_time"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           value="{{ old('start_time') }}" required>
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                    <input type="time" 
                           name="end_time"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           value="{{ old('end_time') }}" required>
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
                <button type="submit" 
                        class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Create Time Slot</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Categories data for JavaScript
const categoriesData = @json($categories);

function updateSubCategories() {
    const categorySelect = document.getElementById('category_id');
    const subCategorySelect = document.getElementById('sub_category_id');
    const selectedCategoryId = categorySelect.value;
    
    // Clear existing options
    subCategorySelect.innerHTML = '<option value="">Select a sub category</option>';
    
    if (selectedCategoryId) {
        const selectedCategory = categoriesData.find(cat => cat.id == selectedCategoryId);
        if (selectedCategory && selectedCategory.sub_categories && selectedCategory.sub_categories.length > 0) {
            selectedCategory.sub_categories.forEach(subCat => {
                const option = document.createElement('option');
                option.value = subCat.id;
                option.textContent = subCat.name;
                subCategorySelect.appendChild(option);
            });
        }
    }
}

// Set default date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="date"]').value = today;
});
</script>
@endsection
