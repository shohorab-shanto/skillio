@extends('frontend.layouts.on-boarding')

@section('title', __('trans.in_person_education_location_title'))
@section('meta_description', __('trans.in_person_education_location_description'))
@section('meta_keywords', __('trans.in_person_education_location_keywords'))

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
        <!-- Logo and Title -->
        <a href="/" class="mt-8 mb-8">
            <img style="height:36px; width"112px;" src="{{ asset('assets/images/logo.png') }}" alt="" class="mx-auto mt-20">
        </a>
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-8 mt-2">{{ __('trans.in_person_education_location_heading') }}</h2>

        <!-- Form Section -->
        <form class="w-full max-w-md flex flex-col items-center" method="POST" action="{{ route('user.onboarding.in_person_education_location.submit') }}">
            @csrf
            <div class="w-full flex flex-col gap-4 mb-8">
                <!-- Country -->
                <div>
                    <label class="text-sm font-medium mb-1 block">{{ __('trans.country') }}*</label>
                    <div class="relative">
                        <select name="country" required
                            class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-purple-500 bg-base-100 border border-gray-200 text-sm appearance-none">
                            <option value="">{{ __('trans.select_country') }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ old('country', isset($selectedCountry) ? $selectedCountry : null) == $country ? 'selected' : '' }}>{{ $country }}</option>
                            @endforeach
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                            <img src="{{ asset('assets/down-arrow-svgrepo-com.svg') }}" alt="Dropdown arrow"
                                class="w-3 h-3 inline-block" />
                        </span>
                    </div>
                </div>
                <!-- City -->
                <div>
                    <label class="text-sm font-medium mb-1 block">{{ __('trans.city') }}*</label>
                    <input type="text"
                        name="city"
                        value="{{ old('city', isset($selectedCity) ? $selectedCity : '') }}"
                        class="w-full px-3 py-2 rounded-lg focus:ring-2 focus:ring-purple-500 bg-base-100 border border-gray-200 text-sm"
                        placeholder="{{ __('trans.enter_your_city') }}" />
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-center gap-4 w-full">
                <a href="{{ route('user.onboarding.in_person_or_online') }}"
                   class="btn border rounded-3xl w-28 h-12 border-purple-700 text-purple-700 bg-transparent hover:bg-purple-700 hover:text-white transition-colors duration-300 flex items-center justify-center">
                    &lt; {{ __('trans.back') }}
                </a>
                <button type="submit"
                        class="btn border-none rounded-3xl w-32 h-12 bg-purple-700 text-white hover:bg-transparent hover:text-purple-700 hover:border-purple-700 transition-colors duration-300 flex items-center justify-center gap-2">
                    {{ __('trans.continue') }} &gt;
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="mb-2 md:mb-6">
        @include('frontend.layouts.footer-onboard')
    </div>
</div>
@endsection
