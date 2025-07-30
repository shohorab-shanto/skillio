@extends('frontend.layouts.on-boarding')

@section('title', 'In Person or Online')
@section('meta_description', 'Choose in-person or online education for your Skillio experience.')
@section('meta_keywords', 'in person, online, education, Skillio, onboarding')

@section('content')
<div class="min-h-screen flex flex-col justify-between bg-gradient-to-br from-pink-50 via-white to-purple-100 px-2 md:px-0">
    <div class="flex-1 flex flex-col items-center justify-center">
        <!-- Logo and Title -->
        <a href="/" class="mt-8 mb-2">
            <img src="{{ asset('assests/images/logo.png') }}" alt="" class="mx-auto mt-20">
        </a>
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-8 mt-2">Do you prefer in-person or online education?</h2>

        <!-- Options -->
        <form class="w-full max-w-3xl flex flex-col items-center" method="POST" action="#">
            @csrf
            <div class="w-full flex flex-col md:flex-row gap-4 mb-8">
                <label class="flex-1 cursor-pointer group">
                    <input type="radio" name="education_mode" value="in_person" class="peer sr-only" checked>
                    <div class="flex flex-row items-center border-2 border-transparent peer-checked:border-purple-600 rounded-xl bg-white px-6 py-5 transition-all duration-200 shadow-sm peer-checked:shadow-lg hover:border-purple-400">
                        <img src="{{ asset('assests/images/in-person.png') }}" alt="In-person education" class="w-12 h-12 mr-4">
                        <div>
                            <span class="font-semibold text-base text-gray-900 mb-1 block">In-person education</span>
                            <span class="text-sm text-gray-500 text-left block">Attend classes or sessions physically.</span>
                        </div>
                    </div>
                </label>
                <label class="flex-1 cursor-pointer group">
                    <input type="radio" name="education_mode" value="online" class="peer sr-only">
                    <div class="flex flex-row items-center border-2 border-transparent peer-checked:border-purple-600 rounded-xl bg-white px-6 py-5 transition-all duration-200 shadow-sm peer-checked:shadow-lg hover:border-purple-400">
                        <img src="{{ asset('assests/images/online.png') }}" alt="Online education" class="w-12 h-12 mr-4">
                        <div>
                            <span class="font-semibold text-base text-gray-900 mb-1 block">Online education</span>
                            <span class="text-sm text-gray-500 text-left block">Learn remotely from anywhere.</span>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-center gap-4 w-full">
                <a href="{{ route('user.onboarding.category_service') }}"
                   class="btn border rounded-3xl w-28 h-12 border-gray-300 text-gray-700 bg-white hover:bg-gray-100 transition-colors duration-200 flex items-center justify-center">
                    &lt; Back
                </a>
                <button type="submit"
                        class="btn border-none rounded-3xl w-32 h-12 bg-purple-600 text-white hover:bg-purple-700 transition-colors duration-200 flex items-center justify-center gap-2">
                    Continue
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="mb-2 md:mb-6">
        @include('frontend.layouts.footer')
    </div>
</div>
@endsection
