@extends('frontend.layouts.on-boarding')

@section('title', __('trans.select_category'))
@section('meta_description', __('trans.select_category_description'))
@section('meta_keywords', __('trans.select_category_keywords'))

@section('content')
<div class="min-h-screen flex flex-col justify-between bg-gradient-to-br from-orange-100 via-white to-purple-100 px-2 md:px-0 relative overflow-hidden">
    <!-- Vertical Lines Background -->
    <div class="absolute inset-0 pointer-events-none hidden md:block">
        <div class="absolute top-0 left-0 w-full h-full">
            <!-- Vertical Line 1 -->
            <div class="absolute top-0 left-[5%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 2 -->
            <div class="absolute top-0 left-[10%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 3 -->
            <div class="absolute top-0 left-[15%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 4 -->
            <div class="absolute top-0 left-[20%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 5 -->
            <div class="absolute top-0 left-[25%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 6 -->
            <div class="absolute top-0 left-[30%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 7 -->
            <div class="absolute top-0 left-[35%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 8 -->
            <div class="absolute top-0 left-[40%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 9 -->
            <div class="absolute top-0 left-[45%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 10 -->
            <div class="absolute top-0 left-[50%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 11 -->
            <div class="absolute top-0 left-[55%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 12 -->
            <div class="absolute top-0 left-[60%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 13 -->
            <div class="absolute top-0 left-[65%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 14 -->
            <div class="absolute top-0 left-[70%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 15 -->
            <div class="absolute top-0 left-[75%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <div class="absolute top-0 left-[75%] w-px h-full bg-gradient-to-br from-purple-100/20 via-transparent via-25% via-75% to-orange-100/20"></div>
            <!-- Vertical Line 16 -->
            <div class="absolute top-0 left-[80%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 17 -->
            <div class="absolute top-0 left-[85%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 18 -->
            <div class="absolute top-0 left-[90%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
            <!-- Vertical Line 19 -->
            <div class="absolute top-0 left-[95%] w-px h-full bg-gradient-to-br from-purple-100/80 via-transparent via-25% via-75% to-orange-100/80"></div>
        </div>
    </div>

    <div class="flex-1 flex flex-col items-center justify-center relative z-10">
        <!-- Logo -->
        <div class="mt-8 mb-8 flex justify-center">
            <a href="/" class="flex items-center">
                <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="h-7 w-20 md:h-9 md:w-28 lg:h-10 lg:w-32 object-contain mt-20">
            </a>
        </div>
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-4 mt-2">{{ __('trans.category_service_heading') }}</h2>
        <p class="text-center mb-6 text-[#605C6D]">
            {{ __('trans.choose_your_skill') }}
        </p>
        <!-- Category Cards Form -->
        <form id="categoryForm" method="POST" action="{{ route('user.onboarding.category_service.submit') }}">
            @csrf
            <section class="w-full max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-5 gap-y-8 justify-items-center mb-8">
                @foreach($categories as $category)
                    @php
                        $isSelected = old('category_id', isset($selectedCategoryId) ? $selectedCategoryId : null) == $category->id;
                    @endphp
                    <label class="flex-1 cursor-pointer group w-full" style="max-width:100%">
                        <input type="radio" name="category_id" value="{{ $category->id }}" class="peer sr-only category-radio" {{ $isSelected ? 'checked' : '' }}>
                        <div class="flex flex-col justify-center bg-white w-full min-h-[130px] max-h-[130px] min-w-[220px] max-w-[320px] p-5 rounded-xl border-2 border-transparent peer-checked:border-purple-600 transition-all duration-200 shadow-sm peer-checked:shadow-lg hover:border-purple-400" onclick="selectCategory(this, {{ $category->id }})">
                            <div class="flex items-center">
                                @if(!empty($category->image))
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 mr-4 object-contain flex-shrink-0">
                                @else
                                    <img src="{{ asset('assets/images/Layer_1.png') }}" alt="" class="w-12 h-12 mr-4 object-contain flex-shrink-0">
                                @endif
                                <div>
                                    <h1 class="font-bold text-base text-gray-900 mb-1">{{ $category->name }}</h1>
                                    @if(strtolower($category->name) == 'others')
                                        <div class="custom-category-input" data-category-id="{{ $category->id }}">
                                            <input type="text" 
                                                   name="custom_category_name" 
                                                   placeholder="{{ __('trans.enter_category_name') }}" 
                                                   value="{{ $category->id == $selectedCategoryId ? $customSubCategoryName : '' }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 custom-category-name-input"
                                                   maxlength="50">
                                            <p class="text-xs text-red-500 mt-1 hidden custom-category-error">{{ __('trans.category_name_required') }}</p>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">
                                            @php
                                                $subCategoriesText = $category->subCategories->pluck('name')->implode(', ');
                                                $maxLength = 55;
                                                if (strlen($subCategoriesText) > $maxLength) {
                                                    $subCategoriesText = substr($subCategoriesText, 0, $maxLength) . '...';
                                                }
                                            @endphp
                                            {{ $subCategoriesText }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </section>
            <!-- Navigation Buttons -->
            <div class="flex justify-center mt-5 w-full">
                <div class="flex gap-3 w-full max-w-xs">
                    <button type="button"
                        class="btn border rounded-3xl w-1/2 h-12 border-purple-700 text-purple-700 bg-transparent hover:bg-purple-700 hover:text-white transition-colors duration-300">
                        &lt; {{ __('trans.back') }}
                    </button>
                    <button type="submit"
                        class="btn border rounded-3xl w-1/2 h-12 hover:border-purple-700 hover:bg-transparent hover:text-purple-700 bg-purple-700 text-white transition-colors duration-300 flex items-center justify-center">
                        {{ __('trans.continue') }} &gt;
                    </button>
                </div>
            </div>
        </form>


    </div>
    <!-- bottom Footer -->
    <div class="mb-2 md:mb-6">
        @include('frontend.layouts.footer-onboard')
    </div>
</div>

@push('scripts')
<script>
// Global function to select category when card is clicked
function selectCategory(element, categoryId) {
    // Find the radio button within this label
    const radio = element.closest('label').querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
        // Trigger change event to update UI
        radio.dispatchEvent(new Event('change'));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const categoryRadios = document.querySelectorAll('.category-radio');
    const form = document.getElementById('categoryForm');

    // Function to handle custom category input focus and show existing value
    function handleCustomCategoryFocus() {
        const selectedRadio = document.querySelector('.category-radio:checked');
        if (selectedRadio) {
            const categoryName = selectedRadio.closest('label').querySelector('h1').textContent;
            if (categoryName.toLowerCase() == 'others') {
                const customInput = selectedRadio.closest('label').querySelector('.custom-category-name-input');
                if (customInput) {
                    customInput.focus();
                    // If there's an existing value, hide error
                    const errorElement = selectedRadio.closest('label').querySelector('.custom-category-error');
                    if (customInput.value.trim() && errorElement) {
                        errorElement.classList.add('hidden');
                    }
                }
            }
        }
    }

    // Add event listeners to all category radio buttons
    categoryRadios.forEach(radio => {
        radio.addEventListener('change', handleCustomCategoryFocus);
    });

    // Initialize on page load to show existing values
    handleCustomCategoryFocus();

    // Form validation
    form.addEventListener('submit', function(e) {
        const selectedRadio = document.querySelector('.category-radio:checked');
        
        if (selectedRadio) {
            const categoryName = selectedRadio.closest('label').querySelector('h1').textContent;
            
            if (categoryName.toLowerCase() == 'others') {
                const customInput = selectedRadio.closest('label').querySelector('.custom-category-name-input');
                const errorElement = selectedRadio.closest('label').querySelector('.custom-category-error');
                
                if (customInput && !customInput.value.trim()) {
                    e.preventDefault();
                    if (errorElement) {
                        errorElement.classList.remove('hidden');
                    }
                    customInput.focus();
                    return false;
                }
                
                if (errorElement) {
                    errorElement.classList.add('hidden');
                }
            }
        }
    });
});
</script>
@endpush
@endsection
