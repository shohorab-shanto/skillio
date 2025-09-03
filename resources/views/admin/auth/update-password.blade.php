@extends('admin.layouts.backend')

@section('title', __('trans.update_password'))

@section('header')
    {{ __('trans.update_password') }}
@endsection

@section('content')
<div class="mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <!-- Page Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-lock text-2xl text-purple-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ __('trans.update_password') }}</h2>
            <p class="text-gray-600">{{ __('trans.update_password_description') }}</p>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fa-solid fa-check-circle text-green-500 mr-3"></i>
                    <div class="text-green-800 text-sm">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fa-solid fa-exclamation-circle text-red-500 mr-3"></i>
                    <div class="text-red-800 text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Password Update Form -->
        <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-6">
            @csrf

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('trans.current_password') }}
                </label>
                <div class="relative">
                    <input id="current_password" type="password" name="current_password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 @error('current_password') border-red-500 @enderror"
                           placeholder="{{ __('trans.enter_current_password') }}">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fa-solid fa-lock text-gray-400"></i>
                    </div>
                </div>
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('trans.new_password') }}
                </label>
                <div class="relative">
                    <input id="password" type="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 @error('password') border-red-500 @enderror"
                           placeholder="{{ __('trans.enter_new_password') }}">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fa-solid fa-key text-gray-400"></i>
                    </div>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">{{ __('trans.password_requirements') }}</p>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('trans.confirm_new_password') }}
                </label>
                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                           placeholder="{{ __('trans.confirm_new_password') }}">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i class="fa-solid fa-key text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fa-solid fa-shield-halved text-blue-500 mr-3 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">{{ __('trans.security_notice') }}</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li>{{ __('trans.password_security_tip_1') }}</li>
                            <li>{{ __('trans.password_security_tip_2') }}</li>
                            <li>{{ __('trans.password_security_tip_3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                    <i class="fa-solid fa-save mr-2"></i>
                    {{ __('trans.update_password') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
