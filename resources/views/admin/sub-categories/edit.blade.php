@extends('admin.layouts.backend')

@section('title', __('trans.edit_sub_category'))

@section('header')
    {{ __('trans.edit_sub_category') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex justify-end items-center gap-3">
        <a href="{{ route('admin.sub-categories.index') }}" 
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            {{ __('trans.back_to_sub_categories') }}
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.sub-categories.update', $subCategory->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Category Selection -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('trans.parent_category') }} <span class="text-red-500">*</span>
                </label>
                <select id="category_id" name="category_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
                    <option value="">{{ __('trans.select_a_category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (old('category_id', $subCategory->category_id) == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sub-Category Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('trans.sub_category_name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $subCategory->name) }}" required
                       placeholder="{{ __('trans.enter_sub_category_name') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">{{ __('trans.sub_category_name_unique') }}</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('trans.description') }}
                </label>
                <textarea id="description" name="description" rows="4"
                          placeholder="{{ __('trans.enter_description_optional') }}"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $subCategory->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">{{ __('trans.provide_brief_description') }}</p>
            </div>



            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.sub-categories.index') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    {{ __('trans.cancel') }}
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-save mr-2"></i>
                    {{ __('trans.update_sub_category') }}
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
                <h3 class="text-sm font-medium text-blue-800 mb-2">{{ __('trans.editing_sub_categories') }}</h3>
                <div class="text-sm text-blue-700 space-y-1">
                    <p>• <strong>{{ __('trans.parent_category') }}:</strong> {{ __('trans.parent_category_change') }}</p>
                    <p>• <strong>{{ __('trans.unique_names_remain') }}</strong> {{ __('trans.unique_names_remain_description') }}</p>

                    <p>• <strong>{{ __('trans.data_integrity') }}</strong> {{ __('trans.data_integrity_description') }}</p>
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
    const categoryId = document.getElementById('category_id').value;
    const name = document.getElementById('name').value.trim();
    
    if (!categoryId) {
        e.preventDefault();
        alert('{{ __('trans.please_select_parent_category') }}');
        document.getElementById('category_id').focus();
        return;
    }
    
    if (!name) {
        e.preventDefault();
        alert('{{ __('trans.please_enter_sub_category_name') }}');
        document.getElementById('name').focus();
        return;
    }
});
</script>
@endpush
