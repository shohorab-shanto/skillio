@extends('frontend.layouts.on-boarding')

@section('title', 'User Login')
@section('meta_description', 'Login to your Skillio account to access courses and features.')
@section('meta_keywords', 'login, user login, Skillio, courses, account access')

@section('content')
<div class="min-h-screen flex flex-col justify-between bg-gradient-to-br from-purple-100 via-white to-purple-100 px-2 md:px-0">
    <div class="flex-1 flex flex-col items-center justify-center">
        <!-- Logo -->
        <a href="/" class="mt-8 mb-2">
            <img src="{{ asset('assests/images/logo.png') }}" alt="" class="mx-auto mt-20">
        </a>
        <h2 class="text-2xl md:text-3xl font-semibold text-center mb-4 mt-2">What type of category or service are you looking for?</h2>
        <p class="text-center mb-6 text-[#605C6D]">
        Choose your skill
        </p>

        <!-- Category Cards -->
        <section class="w-full max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-5 gap-y-8 justify-items-center mb-8">
            <div class="flex items-center bg-white w-full min-h-[90px] p-5 rounded-xl border-2 border-transparent hover:border-purple-400 transition-all duration-200 shadow-sm hover:shadow-lg cursor-pointer">
                <img src="{{ asset('assests/images/Layer_1.png') }}" alt="" class="w-12 h-12 mr-4">
                <div>
                    <h1 class="font-bold text-base text-gray-900 mb-1">E-Commerce & Online Stores</h1>
                    <p class="text-sm text-gray-500">Dropshipping / E-Commerce Print on Demand</p>
                </div>
            </div>
            <div class="flex items-center bg-white w-full min-h-[90px] p-5 rounded-xl border-2 border-transparent hover:border-purple-400 transition-all duration-200 shadow-sm hover:shadow-lg cursor-pointer">
                <img src="{{ asset('assests/images/Layer_1.png') }}" alt="" class="w-12 h-12 mr-4">
                <div>
                    <h1 class="font-bold text-base text-gray-900 mb-1">Agency & Freelance Services</h1>
                    <p class="text-sm text-gray-500">Facebook / Instagram Ads, TikTok Ads</p>
                </div>
            </div>
            <div class="flex items-center bg-white w-full min-h-[90px] p-5 rounded-xl border-2 border-transparent hover:border-purple-400 transition-all duration-200 shadow-sm hover:shadow-lg cursor-pointer">
                <img src="{{ asset('assests/images/Layer_1.png') }}" alt="" class="w-12 h-12 mr-4">
                <div>
                    <h1 class="font-bold text-base text-gray-900 mb-1">Creative & Video Careers</h1>
                    <p class="text-sm text-gray-500">Video Editing YouTube Channel Management</p>
                </div>
            </div>
            <div class="flex items-center bg-white w-full min-h-[90px] p-5 rounded-xl border-2 border-transparent hover:border-purple-400 transition-all duration-200 shadow-sm hover:shadow-lg cursor-pointer">
                <img src="{{ asset('assests/images/Layer_1.png') }}" alt="" class="w-12 h-12 mr-4">
                <div>
                    <h1 class="font-bold text-base text-gray-900 mb-1">E-Commerce & Online Stores</h1>
                    <p class="text-sm text-gray-500">Dropshipping / E-Commerce Print on Demand</p>
                </div>
            </div>
            <div class="flex items-center bg-white w-full min-h-[90px] p-5 rounded-xl border-2 border-transparent hover:border-purple-400 transition-all duration-200 shadow-sm hover:shadow-lg cursor-pointer">
                <img src="{{ asset('assests/images/Layer_1.png') }}" alt="" class="w-12 h-12 mr-4">
                <div>
                    <h1 class="font-bold text-base text-gray-900 mb-1">E-Commerce & Online Stores</h1>
                    <p class="text-sm text-gray-500">Dropshipping / E-Commerce Print on Demand</p>
                </div>
            </div>
            <div class="flex items-center bg-white w-full min-h-[90px] p-5 rounded-xl border-2 border-transparent hover:border-purple-400 transition-all duration-200 shadow-sm hover:shadow-lg cursor-pointer">
                <img src="{{ asset('assests/images/Layer_1.png') }}" alt="" class="w-12 h-12 mr-4">
                <div>
                    <h1 class="font-bold text-base text-gray-900 mb-1">E-Commerce & Online Stores</h1>
                    <p class="text-sm text-gray-500">Dropshipping / E-Commerce Print on Demand</p>
                </div>
            </div>
        </section>

        <!-- Navigation Buttons -->
        <div class="flex justify-center mt-5 w-full">
            <div class="flex gap-3 w-full max-w-xs">
                <button
                    class="btn border rounded-3xl w-1/2 h-12 border-purple-700 text-purple-700 bg-transparent hover:bg-purple-700 hover:text-white transition-colors duration-300">
                    &lt; Back
                </button>
                <a href="{{ route('user.onboarding.in_person_or_online') }}"
                    class="btn border rounded-3xl w-1/2 h-12 hover:border-purple-700 hover:bg-transparent hover:text-purple-700 bg-purple-700 text-white transition-colors duration-300 flex items-center justify-center">
                    Continue &gt;
                </a>
            </div>
        </div>
    </div>
    <!-- bottom Footer -->
    <div class="mb-2 md:mb-6">
        @include('frontend.layouts.footer')
    </div>
</div>
@endsection
