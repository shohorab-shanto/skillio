@extends('frontend.layouts.app')

@section('title', __('trans.payment_failed_title'))

@section('content')
<div class="min-h-screen bg-gray-50 pt-32">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 mb-6">
                <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ __('trans.payment_failed_heading') }}</h1>
            <p class="text-lg text-gray-600">{{ __('trans.payment_failed_message') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Error Details -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('trans.what_happened') }}</h2>
                
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                {{ $error ?? __('trans.payment_processing_failed') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($transaction)
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('trans.transaction_id') }}</span>
                            <span class="font-semibold text-gray-900">{{ $transaction->transaction_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('trans.status') }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                {{ ucfirst($transaction->transaction_status) }}
                            </span>
                        </div>
                        @if($transaction->error_message)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('trans.error') }}:</span>
                                <span class="font-semibold text-red-600 text-sm">{{ $transaction->error_message }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('trans.attempted_at') }}:</span>
                            <span class="font-semibold text-gray-900">{{ $transaction->created_at->format('M d, Y - H:i A') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Common Issues & Solutions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('trans.common_issues_solutions') }}</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-2 w-2 bg-orange-500 rounded-full"></div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ __('trans.insufficient_funds') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('trans.insufficient_funds_description') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-2 w-2 bg-orange-500 rounded-full"></div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ __('trans.card_information') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('trans.card_information_description') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-2 w-2 bg-orange-500 rounded-full"></div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ __('trans.bank_restrictions') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('trans.bank_restrictions_description') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-2 w-2 bg-orange-500 rounded-full"></div>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ __('trans.network_issues') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('trans.network_issues_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('trans.what_can_you_do') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100 mb-4">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('trans.try_again') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('trans.try_again_description') }}</p>
                </div>
                
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-lg bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('trans.contact_support_failure') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('trans.contact_support_failure_description') }}</p>
                </div>
                
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100 mb-4">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('trans.different_method') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('trans.different_method_description') }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
            @if($type && $itemId)
                <a href="{{ $type === 'session' ? route('checkout.session', $itemId) : route('checkout.course', $itemId) }}" 
                   class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    {{ __('trans.try_payment_again') }}
                </a>
            @endif
            
            <a href="{{ route('mentors') }}" 
               class="inline-flex items-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg border-2 border-gray-300 hover:bg-gray-50 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ __('trans.back_to_mentors') }}
            </a>
            
            <a href="{{ Auth::user()->isMentor() ? route('mentor.dashboard') : route('user.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                </svg>
                {{ __('trans.go_to_dashboard') }}
            </a>
        </div>

        <!-- Support Contact -->
        <div class="text-center mt-8">
            <p class="text-gray-600">
                {{ __('trans.need_help') }} 
                <a href="mailto:support@skillio.com" class="text-purple-600 hover:text-purple-700 font-medium">support@skillio.com</a>
                {{ __('trans.or_call_us_at') }} 
                <a href="tel:+1234567890" class="text-purple-600 hover:text-purple-700 font-medium">+1 (234) 567-8900</a>
            </p>
        </div>
    </div>
</div>
@endsection
