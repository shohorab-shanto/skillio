<x-guest-layout>
    <!-- Skillio Logo -->
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-xl mb-6">
        <img src="{{ asset('assets/images/login.png') }}" alt="Skillio Logo">
    </div>

    <!-- Header Section -->
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-center">{{ __('trans.reset_password') }}</h2>
        <p class="text-sm text-gray-500 text-center">Create a strong password to secure your account</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="space-y-1">
            <x-input-label for="email" :value="__('trans.email_address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="{{ __('trans.email_placeholder') }}" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <x-input-label for="password" :value="__('trans.password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Enter your new password" />
            <p class="text-gray-500 text-sm flex items-center justify-center gap-1">
                <span class="w-3 h-3 rounded-full bg-gray-200 text-black flex items-center justify-center text-sm">!</span>
                {{ __('trans.password_hint') }}
            </p>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-1">
            <x-input-label for="password_confirmation" :value="__('trans.confirm_password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your new password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <x-primary-button class="w-full">
                {{ __('trans.reset_password') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Back to Login -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-purple-600 hover:text-purple-700 transition-colors duration-200">
            ← Back to Login
        </a>
    </div>
</x-guest-layout>
