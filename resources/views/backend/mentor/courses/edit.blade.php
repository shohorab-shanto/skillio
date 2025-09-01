@extends('backend.layouts.app')

@section('title', 'Edit Course')

@section('styles')
<style>
/* Multi-select dropdown styling */
#subcategory-toggle {
    min-height: 42px;
}

#subcategory-toggle:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

#subcategory-dropdown {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Selected tag styling */
.selected-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    background-color: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 12px;
    color: #374151;
}

.selected-tag button {
    padding: 2px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.selected-tag button:hover {
    background-color: #e5e7eb;
}

/* Dropdown option styling */
.dropdown-option {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    color: #374151;
}

.dropdown-option:hover {
    background-color: #f3f4f6;
}

.dropdown-option.selected {
    background-color: #f3f4f6;
}

/* Checkmark styling */
.check-icon {
    color: #8b5cf6;
    opacity: 0;
    transition: opacity 0.2s;
}

.dropdown-option.selected .check-icon {
    opacity: 1;
}

/* Custom scrollbar for dropdown */
#subcategory-dropdown::-webkit-scrollbar {
    width: 8px;
}

#subcategory-dropdown::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

#subcategory-dropdown::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

#subcategory-dropdown::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for chevron */
#dropdown-chevron {
    transition: transform 0.2s ease;
}

/* Focus states */
.dropdown-option:focus {
    outline: none;
    background-color: #f3f4f6;
}
</style>
@endsection

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('mentor.courses.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Course</h1>
            </div>
        </div>
        
        <!-- Course Status -->
        <div class="flex items-center space-x-3">
            @if($course->status == 'approved')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="fa-solid fa-check mr-1"></i>
                    Approved
                </span>
            @elseif($course->status == 'pending')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i class="fa-solid fa-clock mr-1"></i>
                    Pending Approval
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <i class="fa-solid fa-times mr-1"></i>
                    Rejected
                </span>
            @endif

            @if($course->needs_reapproval)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                    Needs Reapproval
                </span>
            @endif

            <!-- Delete Button -->
            <button type="button" onclick="showDeleteModal()" 
                    class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-red-100 text-red-700 hover:bg-red-200 transition-colors">
                <i class="fa-solid fa-trash mr-1"></i>
                Delete Course
            </button>
        </div>
    </div>
@endsection

@section('content')
<div class="w-full">
    @if($course->status == 'approved')
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Course Update Notice
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>This course is currently approved and live. Any significant changes will require admin approval before being reflected to students.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($course->rejection_reason)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Rejection Reason
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $course->rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('mentor.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Course Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Course Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Course Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Course Title *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" 
                           maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           placeholder="e.g., Complete Web Development Bootcamp" required
                           oninput="updateCharacterCount('title', 'title-count', 255)">
                    <p class="text-xs text-gray-500 mt-1">
                        <span id="title-count">0</span>/255 characters
                    </p>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select id="category_id" name="category_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                            required onchange="updateSubCategories()">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub Categories -->
                <div class="relative">
                    <label for="sub_category_ids" class="block text-sm font-medium text-gray-700 mb-2">Sub Categories *</label>
                    
                    <!-- Multi-Select Dropdown -->
                    <div class="relative">
                        <!-- Toggle Button -->
                        <button type="button" id="subcategory-toggle" 
                                class="relative py-3 ps-4 pe-9 flex gap-x-2 text-nowrap w-full cursor-pointer bg-white border border-gray-300 rounded-lg text-start text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 hover:border-gray-400 transition-colors"
                                onclick="toggleSubcategoryDropdown()" aria-expanded="false">
                            <div class="flex flex-wrap gap-1 flex-1" id="selected-display">
                                <span class="text-gray-500" id="placeholder-text">Select sub-categories...</span>
                            </div>
                            <!-- Dropdown Arrow -->
                            <div class="absolute top-1/2 end-3 -translate-y-1/2">
                                <svg id="dropdown-chevron" class="shrink-0 size-3.5 text-gray-500 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </div>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="subcategory-dropdown" class="hidden absolute mt-2 z-50 w-full max-h-72 p-1 space-y-0.5 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300">
                            <div class="text-center py-4 text-sm text-gray-500" id="dropdown-placeholder">
                                Please select a category first
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs for form submission -->
                    <div id="hidden-inputs"></div>
                    
                    @error('sub_category_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (USD) *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" id="price" name="price" value="{{ old('price', $course->price) }}" 
                               step="0.01" min="0"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                               placeholder="0.00" required>
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Discount -->
                <div>
                    <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">Discount (%)</label>
                    <input type="number" id="discount" name="discount" value="{{ old('discount', $course->discount) }}" 
                           min="0" max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           placeholder="0">
                    @error('discount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Course Duration -->
                <div>
                    <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">Course Duration (Days) *</label>
                    <input type="number" id="duration_days" name="duration_days" value="{{ old('duration_days', $course->duration_days) }}" 
                           min="1" max="365"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           placeholder="e.g., 30" required>
                    <p class="text-xs text-gray-500 mt-1">Enter the number of days for the course (1-365 days)</p>
                    @error('duration_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Course Description *</label>
                    <textarea id="description" name="description" rows="6" 
                              maxlength="5000"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                              placeholder="Describe your course, what students will learn, prerequisites, and what makes it unique..." required
                              oninput="updateCharacterCount('description', 'description-count', 5000)">{{ old('description', $course->description) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <span id="description-count">0</span>/5,000 characters
                    </p>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Course Images -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Course Image</h2>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Course Image -->
                <div>
                    <label for="course_image" class="block text-sm font-medium text-gray-700 mb-2">Course Image</label>
                    
                    @if($course->cover_photo)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $course->cover_photo) }}" alt="Current course image" class="w-full h-48 object-cover rounded-lg border">
                            <p class="text-xs text-gray-500 mt-1">Current course image</p>
                        </div>
                    @endif
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                        <div class="space-y-1 text-center flex flex-col items-center justify-center">
                            <div class="flex flex-col items-center justify-center text-sm text-gray-600 text-center">
                                <label for="course_image" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                    <span>{{ $course->cover_photo ? 'Change course image' : 'Upload course image' }}</span>
                                    <input id="course_image" name="course_image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this, 'course-image-preview')">
                                </label>
                                <p class="pl-1 mt-2">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            <p class="text-xs text-gray-400">This image will be used as cover photo and automatically resized for thumbnail</p>
                            <div id="course-image-preview" class="mt-4 flex justify-center"></div>
                        </div>
                    </div>
                    @error('course_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('mentor.courses.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    Back to Courses
                </a>
                
                <div class="flex items-center space-x-3">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-save mr-2"></i>
                        Update Course
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <!-- Warning Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fa-solid fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            
            <!-- Modal Title -->
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Course</h3>
            
            <!-- Modal Message -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Are you sure you want to delete "<span class="font-medium text-gray-900">{{ $course->title }}</span>"?
                </p>
                <p class="text-xs text-red-600">
                    This action cannot be undone. All course data including images will be permanently removed.
                </p>
            </div>
            
            <!-- Modal Buttons -->
            <div class="flex items-center justify-center space-x-4 mt-6">
                <button type="button" onclick="hideDeleteModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="confirmDelete()" 
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fa-solid fa-trash mr-1"></i>
                    Delete Course
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('mentor.courses.delete', $course) }}" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
// Categories and sub-categories data
const categoriesData = @json($categories);
const existingSubCategories = @json($selectedSubCategories);
let selectedSubCategories = [];
let isDropdownOpen = false;

function toggleSubcategoryDropdown() {
    const dropdown = document.getElementById('subcategory-dropdown');
    const toggle = document.getElementById('subcategory-toggle');
    const chevron = document.getElementById('dropdown-chevron');
    
    if (isDropdownOpen) {
        closeSubcategoryDropdown();
    } else {
        openSubcategoryDropdown();
    }
}

function openSubcategoryDropdown() {
    const dropdown = document.getElementById('subcategory-dropdown');
    const toggle = document.getElementById('subcategory-toggle');
    const chevron = document.getElementById('dropdown-chevron');
    
    dropdown.classList.remove('hidden');
    toggle.setAttribute('aria-expanded', 'true');
    chevron.style.transform = 'rotate(180deg)';
    isDropdownOpen = true;
    
    // Close dropdown when clicking outside
    document.addEventListener('click', handleOutsideClick);
}

function closeSubcategoryDropdown() {
    const dropdown = document.getElementById('subcategory-dropdown');
    const toggle = document.getElementById('subcategory-toggle');
    const chevron = document.getElementById('dropdown-chevron');
    
    dropdown.classList.add('hidden');
    toggle.setAttribute('aria-expanded', 'false');
    chevron.style.transform = 'rotate(0deg)';
    isDropdownOpen = false;
    
    document.removeEventListener('click', handleOutsideClick);
}

function handleOutsideClick(event) {
    const container = event.target.closest('#subcategory-toggle') || event.target.closest('#subcategory-dropdown');
    if (!container) {
        closeSubcategoryDropdown();
    }
}

function updateSubCategories() {
    const categorySelect = document.getElementById('category_id');
    const dropdown = document.getElementById('subcategory-dropdown');
    const selectedCategoryId = categorySelect.value;
    
    // Close dropdown when category changes
    closeSubcategoryDropdown();
    
    if (selectedCategoryId) {
        const selectedCategory = categoriesData.find(cat => cat.id == selectedCategoryId);
        if (selectedCategory && selectedCategory.sub_categories && selectedCategory.sub_categories.length > 0) {
            dropdown.innerHTML = '';
            
            selectedCategory.sub_categories.forEach(subCat => {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'py-2 px-4 w-full text-sm text-gray-800 cursor-pointer hover:bg-gray-100 rounded-lg focus:outline-none focus:bg-gray-100 transition-colors';
                optionDiv.dataset.id = subCat.id;
                optionDiv.dataset.name = subCat.name;
                
                // Check if this sub-category is already selected
                const isSelected = selectedSubCategories.find(item => item.id == subCat.id);
                if (isSelected) {
                    optionDiv.classList.add('bg-purple-50');
                }
                
                optionDiv.innerHTML = `
                    <div class="flex justify-between items-center w-full">
                        <span>${subCat.name}</span>
                        <span class="selected-check ${isSelected ? '' : 'hidden'}">
                            <svg class="shrink-0 size-3.5 text-purple-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </span>
                    </div>
                `;
                
                optionDiv.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSubCategory(subCat.id, subCat.name, this);
                });
                
                dropdown.appendChild(optionDiv);
            });
        } else {
            dropdown.innerHTML = '<div class="text-center py-4 text-sm text-gray-500">No sub-categories available for this category</div>';
        }
    } else {
        dropdown.innerHTML = '<div class="text-center py-4 text-sm text-gray-500">Please select a category first</div>';
    }
}

function toggleSubCategory(id, name, element) {
    const index = selectedSubCategories.findIndex(item => item.id == id);
    const checkIcon = element.querySelector('.selected-check');
    
    if (index > -1) {
        // Remove from selected
        selectedSubCategories.splice(index, 1);
        element.classList.remove('bg-purple-50');
        checkIcon.classList.add('hidden');
    } else {
        // Add to selected
        selectedSubCategories.push({ id: id, name: name });
        element.classList.add('bg-purple-50');
        checkIcon.classList.remove('hidden');
    }
    
    updateSelectedDisplay();
    updateHiddenInputs();
}

function updateSelectedDisplay() {
    const container = document.getElementById('selected-display');
    const placeholder = document.getElementById('placeholder-text');
    
    container.innerHTML = '';
    
    if (selectedSubCategories.length == 0) {
        container.appendChild(placeholder);
    } else {
        // Create selected tags
        selectedSubCategories.forEach(subCat => {
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-x-1 py-1 px-2 rounded-full text-xs font-medium bg-purple-100 text-purple-800';
            tag.innerHTML = `
                ${subCat.name}
                <button type="button" class="shrink-0 size-4 inline-flex items-center justify-center rounded-full hover:bg-purple-200 focus:outline-none focus:bg-purple-200 transition-colors" 
                        onclick="removeSubCategory(event, '${subCat.id}')">
                    <svg class="shrink-0 size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 6-12 12"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            `;
            container.appendChild(tag);
        });
        
        // Add count if many items selected
        if (selectedSubCategories.length > 3) {
            const countBadge = document.createElement('span');
            countBadge.className = 'text-xs text-gray-500';
            countBadge.textContent = `+${selectedSubCategories.length - 3} more`;
            container.appendChild(countBadge);
        }
    }
}

function removeSubCategory(event, id) {
    event.stopPropagation(); // Prevent dropdown from opening when removing tag
    
    const index = selectedSubCategories.findIndex(item => item.id == id);
    if (index > -1) {
        selectedSubCategories.splice(index, 1);
        
        // Update the option in dropdown
        const optionElement = document.querySelector(`[data-id="${id}"]`);
        if (optionElement) {
            optionElement.classList.remove('bg-purple-50');
            const checkIcon = optionElement.querySelector('.selected-check');
            if (checkIcon) {
                checkIcon.classList.add('hidden');
            }
        }
        
        updateSelectedDisplay();
        updateHiddenInputs();
    }
}

function updateHiddenInputs() {
    const hiddenContainer = document.getElementById('hidden-inputs');
    hiddenContainer.innerHTML = '';
    
    selectedSubCategories.forEach(subCat => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'sub_category_ids[]';
        input.value = subCat.id;
        hiddenContainer.appendChild(input);
    });
}

function initializeSelectedSubCategories() {
    // Initialize with existing sub-categories
    if (existingSubCategories && existingSubCategories.length > 0) {
        const categorySelect = document.getElementById('category_id');
        const selectedCategoryId = categorySelect.value;
        
        if (selectedCategoryId) {
            const selectedCategory = categoriesData.find(cat => cat.id == selectedCategoryId);
            if (selectedCategory && selectedCategory.sub_categories) {
                existingSubCategories.forEach(subCatId => {
                    const subCategory = selectedCategory.sub_categories.find(sc => sc.id == subCatId);
                    if (subCategory) {
                        selectedSubCategories.push({ id: subCategory.id, name: subCategory.name });
                    }
                });
                
                updateSelectedDisplay();
                updateHiddenInputs();
            }
        }
    }
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" class="max-w-full h-32 object-cover rounded-lg" alt="Preview">
                <p class="text-xs text-gray-500 mt-2">${input.files[0].name}</p>
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}



function updateCharacterCount(inputId, countId, maxLength) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(countId);
    const currentLength = input.value.length;
    
    counter.textContent = currentLength;
    
    // Change color based on percentage of characters used
    if (currentLength >= maxLength * 0.9) {
        counter.className = 'text-red-500 font-medium';
    } else if (currentLength >= maxLength * 0.7) {
        counter.className = 'text-orange-500 font-medium';
    } else {
        counter.className = 'text-gray-500';
    }
}

// Initialize sub-categories on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSubCategories();
    // Initialize with existing selected sub-categories after DOM is ready
    setTimeout(initializeSelectedSubCategories, 100);
    

    
    // Initialize character counts
    updateCharacterCount('title', 'title-count', 255);
    updateCharacterCount('description', 'description-count', 5000);
});

// Delete Modal Functions
function showDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function hideDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Re-enable scrolling
}

function confirmDelete() {
    document.getElementById('deleteForm').submit();
}

// Close modal when clicking outside of it
document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        hideDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key == 'Escape') {
        hideDeleteModal();
    }
});
</script>
@endsection
