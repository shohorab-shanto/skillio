@extends('frontend.layouts.app')

@section('title', __('trans.payment_successful_title'))

@section('content')
<div class="min-h-screen bg-gray-50 pt-32">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ __('trans.payment_successful_heading') }}</h1>
            <p class="text-lg text-gray-600">{{ __('trans.payment_successful_message') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Payment Details -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('trans.payment_details') }}</h2>
                
                @if($transaction)
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('trans.transaction_id') }}</span>
                            <span class="font-semibold text-gray-900">{{ $transaction->transaction_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('trans.amount_paid') }}</span>
                            <span class="font-semibold text-green-600">${{ number_format($transaction->gross_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('trans.payment_method') }}</span>
                            <span class="font-semibold text-gray-900">
                                {{ ucfirst($transaction->payment_method_type ?? 'Card') }}
                                @if($transaction->payment_method_last4)
                                    {{ __('trans.ending_in') }} {{ $transaction->payment_method_last4 }}
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('trans.status') }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ ucfirst($transaction->transaction_status) }}
                            </span>
                        </div>
                        @if($transaction->created_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('trans.date') }}</span>
                                <span class="font-semibold text-gray-900">{{ $transaction->created_at->format('M d, Y - H:i A') }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-600">{{ __('trans.payment_completed_successfully') }}</p>
                    </div>
                @endif
            </div>

            <!-- Enrollment Details -->
            @if($enrollment)
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        @if($enrollment->enrollable_type == 'App\Models\SessionBooking')
                            {{ __('trans.session_booking_details') }}
                        @else
                            {{ __('trans.course_enrollment_details') }}
                        @endif
                    </h2>
                    
                    <div class="space-y-4">
                        @if($enrollment->enrollable_type == 'App\Models\SessionBooking')
                            <!-- Session Details -->
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('trans.mentor') }}</span>
                                <span class="font-semibold text-gray-900">{{ $enrollment->enrollable->mentor->user->name }}</span>
                            </div>
                            @if($enrollment->enrollable->session_date)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('trans.session_date') }}</span>
                                    <span class="font-semibold text-gray-900">{{ $enrollment->enrollable->session_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                            @if($enrollment->enrollable->start_time && $enrollment->enrollable->end_time)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ __('trans.time') }}</span>
                                    <span class="font-semibold text-gray-900">
                                        {{ $enrollment->enrollable->start_time->format('H:i A') }} - 
                                        {{ $enrollment->enrollable->end_time->format('H:i A') }}
                                    </span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('trans.duration') }}</span>
                                <span class="font-semibold text-gray-900">
                                    @php
                                        $startTime = \Carbon\Carbon::parse($enrollment->enrollable->start_time);
                                        $endTime = \Carbon\Carbon::parse($enrollment->enrollable->end_time);
                                        $totalMinutes = $startTime->diffInMinutes($endTime);
                                        $hours = intval($totalMinutes / 60);
                                        $minutes = $totalMinutes % 60;
                                    @endphp
                                    @if($hours > 0 && $minutes > 0)
                                        {{ $hours }}h {{ $minutes }}m
                                    @elseif($hours > 0)
                                        {{ $hours }} hour{{ $hours > 1 ? 's' : '' }}
                                    @else
                                        {{ $minutes }} minute{{ $minutes > 1 ? 's' : '' }}
                                    @endif
                                </span>
                            </div>
                        @else
                            <!-- Course Details -->
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('trans.course') }}</span>
                                <span class="font-semibold text-gray-900">{{ $enrollment->enrollable->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('trans.instructor') }}</span>
                                <span class="font-semibold text-gray-900">{{ $enrollment->enrollable->mentor->user->name }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('trans.enrollment_status') }}</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($enrollment->enrollment_status) }}
                            </span>
                        </div>
                        @if($enrollment->enrolled_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('trans.enrolled_at') }}</span>
                                <span class="font-semibold text-gray-900">{{ $enrollment->enrolled_at->format('M d, Y - H:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Next Steps -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('trans.whats_next') }}</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-purple-100">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8h0M8 21h8a2 2 0 002-2V9a2 2 0 00-2-2H8a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('trans.check_your_dashboard') }}</h3>
                        <p class="text-gray-600">{{ __('trans.dashboard_description') }}</p>
                    </div>
                </div>
                
                <!-- <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Email Confirmation</h3>
                        <p class="text-gray-600">You'll receive a confirmation email with all the details shortly.</p>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
            <a href="{{ Auth::user()->isMentor() ? route('mentor.dashboard') : route('user.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                </svg>
                {{ __('trans.go_to_dashboard') }}
            </a>
            
            <a href="{{ route('mentors') }}" 
               class="inline-flex items-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg border-2 border-purple-600 hover:bg-purple-50 transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                {{ __('trans.browse_more_mentors') }}
            </a>
        </div>
    </div>
</div>
@endsection
