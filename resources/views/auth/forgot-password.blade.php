<x-guest-layout>
    <!-- Skillio Logo -->
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-xl">
        <img src="{{ asset('assets/images/login.png') }}" alt="Skillio Logo">
    </div>

    <h2 class="text-xl font-semibold text-center">{{ __('trans.forgot_password') }}</h2>
    <p class="text-sm text-gray-500 text-center">{{ __('trans.forgot_password_description') }}</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4 mt-2">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1">
            <x-input-label for="email" :value="__('trans.email_address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="{{ __('trans.email_placeholder') }}" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button>
                {{ __('trans.email_password_reset_link') }}
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
