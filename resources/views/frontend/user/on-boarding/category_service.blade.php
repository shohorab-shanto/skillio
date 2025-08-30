@extends('frontend.layouts.on-boarding')

@section('title', __('trans.select_category'))
@section('meta_description', __('trans.select_category_description'))
@section('meta_keywords', __('trans.select_category_keywords'))

@section('content')
<div class="min-h-screen flex flex-col justify-between bg-gradient-to-br from-purple-100 via-white to-purple-100 px-2 md:px-0">
    <div class="flex-1 flex flex-col items-center justify-center">
        <!-- Logo -->
        <a href="/" class="mt-8 mb-2">
            <img style="height:36px; width"112px;" src="{{ asset('assets/images/logo.png') }}" alt="" class="mx-auto mt-20">
        </a>
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
                        <input type="radio" name="category_id" value="{{ $category->id }}" class="peer sr-only" {{ $isSelected ? 'checked' : '' }}>
                        <div class="flex flex-col bg-white w-full min-h-[130px] max-h-[130px] min-w-[220px] max-w-[320px] p-5 rounded-xl border-2 border-transparent peer-checked:border-purple-600 transition-all duration-200 shadow-sm peer-checked:shadow-lg hover:border-purple-400">
                            <div class="flex items-center mb-2">
                                @if(!empty($category->image))
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-12 h-12 mr-4">
                                @else
                                    <img src="{{ asset('assets/images/Layer_1.png') }}" alt="" class="w-12 h-12 mr-4">
                                @endif
                                <div>
                                    <h1 class="font-bold text-base text-gray-900 mb-1">{{ $category->name }}</h1>
                                    <p class="text-sm text-gray-500">
                                        {{ $category->subCategories->pluck('name')->implode(', ') }}
                                    </p>
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
@endsection
