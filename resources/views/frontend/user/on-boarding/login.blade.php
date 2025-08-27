@extends('frontend.layouts.on-boarding')

@section('title', __('trans.login_title'))
@section('meta_description', __('trans.login_meta_description'))
@section('meta_keywords', __('trans.login_meta_keywords'))

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 min-h-screen bg-gradient-to-b from-pink-50 to-white">

        <!-- Left Panel -->
        <div class="flex flex-col justify-between px-8 py-12 md:px-24 bg-gradient-to-b from-pink-50 to-white">
            <!-- Logo header -->
            <a href="/"><img src="{{ asset('assets/images/logo.png') }}" alt=""></a>
            {{-- center --}}
            <div>
                <div class="max-w-md w-full mx-auto space-y-6">
                    <!-- Icon -->
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-xl">
                        <img src="{{ asset('assets/images/login.png') }}" alt="">
                    </div>

                    <h2 class="text-xl font-semibold text-center">{{ __('trans.login_heading') }}</h2>
                    <p class="text-sm text-gray-500 text-center">{{ __('trans.login_subheading') }}</p>

                    <!-- Social Login -->
                    <div class="flex gap-4">
                        <a href="{{ route('login.apple') }}"
                            class="flex-1 px-4 py-2 border-none bg-base-100 rounded-md flex items-center justify-center gap-2 text-sm">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg"
                                class="w-4 h-4" />
                        </a>
                        <a href="{{ route('login.google') }}"
                            class="flex-1 px-4 py-2 border-none bg-base-100 rounded-md flex items-center justify-center gap-2 text-sm">
                            <img src="{{ asset('assets/images/google.png') }}" class="w-4 h-4" />
                        </a>
                    </div>

                    <div class="text-center text-gray-400 text-sm">{{ __('trans.or') }}</div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login.post') }}" class="space-y-4 mt-2">
                        @csrf

                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative text-sm">
                                {{ session('error') }}
                            </div>
                        @endif

                        @error('email')
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative text-sm">
                                {{ $message }}
                            </div>
                        @enderror

                        <!-- Email -->
                        <div class="space-y-1">
                            <label class="text-sm font-medium" for="email">{{ __('trans.email_address') }}*</label>
                            <input type="email" name="email" id="email" placeholder="{{ __('trans.email_placeholder') }}"
                                class="w-full px-4 py-2 border-none bg-base-100 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                required />
                        </div>

                        <!-- Password -->
                        <div class="space-y-1">
                            <label class="text-sm font-medium" for="password">{{ __('trans.password') }}*</label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                    class="w-full px-4 py-2 border-none bg-base-100 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 pr-10"
                                    required />
                                <span
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 cursor-pointer"
                                    onclick="togglePasswordVisibility()"
                                    id="togglePassword">
                                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.293-3.95M6.634 6.634A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.293 5.95M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                    </svg>
                                </span>
                            </div>
                        </div>                        

                        <!-- Checkbox + Forgot -->
                        <div class="flex justify-between items-center text-sm">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="remember" class="accent-purple-500" />
                                {{ __('trans.keep_me_logged_in') }}
                            </label>
                            <a href="{{ route('password.request') }}" class="text-purple-500 hover:underline">{{ __('trans.forgot_password') }}</a>
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-md transition duration-300">
                            {{ __('trans.login_button') }}
                        </button>
                    </form>

                    <!-- Register Link -->
                    <p class="text-center text-sm text-gray-600">
                        {{ __('trans.dont_have_account') }}
                        <a href="{{ route('user.onboarding.register') }}" class="text-purple-600 hover:underline">{{ __('trans.register') }}</a>
                    </p>
                </div>
            </div>
            <!-- bottom Footer -->
            @include('frontend.layouts.footer-onboard')
        </div>

        <!-- Right Panel -->
        <div class="hidden md:flex w-full h-full items-center justify-center p-2">
            <!-- Clouds (Use your own assets or local ones) -->
            <img src="{{ asset('assets/images/login-image-cloud.png') }}" alt="Login background" class="max-w-full max-h-full object-contain" />
        </div>
    </div>
@endsection
