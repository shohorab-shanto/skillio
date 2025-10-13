@extends('admin.layouts.backend')

@section('title', __('trans.create_slider'))

@section('header')
    {{ __('trans.create_new_slider') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex justify-end items-center gap-3">
        <a href="{{ route('admin.sliders.index') }}" 
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            {{ __('trans.back_to_sliders') }}
        </a>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Slider Image -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('trans.slider_image_required') }} <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors duration-200">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                <span>{{ __('trans.upload_image') }}</span>
                                <input id="image" name="image" type="file" accept="image/*" class="sr-only" onchange="previewImage(this)" required>
                            </label>
                            <p class="pl-1">{{ __('trans.or_drag_drop') }}</p>
                        </div>
                        <p class="text-xs text-gray-500">{{ __('trans.recommended_size_slider') }}</p>
                    </div>
                </div>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="image-preview" class="mt-4 hidden">
                    <img id="preview-img" src="" alt="Preview" class="max-h-64 w-auto object-cover rounded-lg mx-auto border-2 border-gray-200">
                </div>
            </div>

            <!-- Order -->
            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('trans.display_order_required') }} <span class="text-red-500">*</span>
                </label>
                <input type="number" id="order" name="order" value="{{ old('order', $maxOrder + 1) }}" required min="0"
                       placeholder="0"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('order') border-red-500 @enderror">
                @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">{{ __('trans.lower_numbers_first') }}. {{ __('trans.current_max') }} {{ $maxOrder }}</p>
            </div>

            <!-- Hidden Status Field (default to active) -->
            <input type="hidden" name="status" value="active">

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.sliders.index') }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    {{ __('trans.cancel') }}
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-save mr-2"></i>
                    {{ __('trans.create_slider') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
