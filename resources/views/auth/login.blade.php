<x-guest-layout>
    <!-- Skillio Logo -->
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-xl">
        <img src="{{ asset('assets/images/login.png') }}" alt="Skillio Logo">
    </div>

    <h2 class="text-xl font-semibold text-center">{{ __('trans.login_heading') }}</h2>
    <p class="text-sm text-gray-500 text-center">{{ __('trans.login_subheading') }}</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4 mt-2">
        @csrf

        <!-- Email Address -->
        <div class="space-y-1">
            <x-input-label for="email" :value="__('trans.email_address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="{{ __('trans.email_placeholder') }}" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <x-input-label for="password" :value="__('trans.password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex justify-between items-center text-sm">
            <label class="flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" class="accent-purple-500" />
                {{ __('trans.remember_me') }}
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-purple-600 hover:text-purple-700 transition-colors duration-200">
                    {{ __('trans.forgot_password') }}
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div class="flex items-center justify-end">
            <x-primary-button>
                {{ __('trans.login_button') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Register Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            {{ __('trans.dont_have_account') }}
            <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-700 transition-colors duration-200">
                {{ __('trans.register') }}
            </a>
        </p>
    </div>
</x-guest-layout>
