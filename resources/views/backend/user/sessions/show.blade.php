@extends('backend.layouts.app')

@section('title', 'Session Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('user.sessions') }}" 
               class="inline-flex items-center text-purple-600 hover:text-purple-700 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Sessions
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Session Details</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Session Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($enrollment->enrollment_status === 'active') bg-green-100 text-green-800
                        @elseif($enrollment->enrollment_status === 'completed') bg-blue-100 text-blue-800
                        @elseif($enrollment->enrollment_status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($enrollment->enrollment_status) }}
                    </span>
                    
                    @if($enrollment->enrollable->date)
                        <span class="text-lg font-semibold text-gray-900">
                            {{ $enrollment->enrollable->date->format('l, M d, Y') }}
                        </span>
                    @endif
                </div>
                
                <div class="text-right">
                    <div class="text-2xl font-bold text-purple-600">
                        ${{ number_format($enrollment->amount, 2) }}
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ ucfirst($enrollment->payment_status) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Mentor Information -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="flex items-start space-x-6">
                    <!-- Mentor Photo -->
                    <div class="flex-shrink-0">
                        @php
                            $mentorPhoto = $enrollment->enrollable->mentor->photo 
                                ? (Str::startsWith($enrollment->enrollable->mentor->photo, ['http://', 'https://', '/storage/']) 
                                    ? $enrollment->enrollable->mentor->photo 
                                    : Storage::url($enrollment->enrollable->mentor->photo))
                                : asset('assets/images/user-avatar.png');
                        @endphp
                        <img src="{{ $mentorPhoto }}" 
                             alt="{{ $enrollment->enrollable->mentor->user->name }}"
                             class="w-20 h-20 rounded-full object-cover border-3 border-white shadow-lg">
                    </div>

                    <!-- Mentor Details -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h2 class="text-xl font-bold text-gray-900">
                                {{ $enrollment->enrollable->mentor->user->name }}
                            </h2>
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <span class="text-sm text-gray-500">Your Mentor</span>
                        </div>
                        @if($enrollment->enrollable->mentor->bio)
                            <p class="text-gray-600 mb-4 leading-relaxed">{{ $enrollment->enrollable->mentor->bio }}</p>
                        @endif
                        
                        <!-- Categories -->
                        @if($enrollment->enrollable->subCategories && count($enrollment->enrollable->subCategories) > 0)
                            <div class="flex flex-wrap gap-2">
                                @if($enrollment->enrollable->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $enrollment->enrollable->category->name }}
                                    </span>
                                @endif
                                @foreach($enrollment->enrollable->subCategories as $subCategory)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $subCategory->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Session Information -->
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                        Session Information
                    </h3>
                        <div class="space-y-4">
                            @if($enrollment->enrollable->date)
                                <div class="flex items-center">
                                    <i class="fas fa-calendar w-5 text-purple-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Date</p>
                                        <p class="font-medium">{{ $enrollment->enrollable->date->format('l, M d, Y') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->enrollable->start_time && $enrollment->enrollable->end_time)
                                <div class="flex items-center">
                                    <i class="fas fa-clock w-5 text-purple-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Time</p>
                                        <p class="font-medium">
                                            {{ $enrollment->enrollable->start_time->format('H:i A') }} - 
                                            {{ $enrollment->enrollable->end_time->format('H:i A') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->enrollable->start_time && $enrollment->enrollable->end_time)
                                @php
                                    $duration = $enrollment->enrollable->start_time->diffInMinutes($enrollment->enrollable->end_time);
                                @endphp
                                <div class="flex items-center">
                                    <i class="fas fa-hourglass-half w-5 text-purple-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Duration</p>
                                        <p class="font-medium">{{ $duration }} minutes</p>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->enrollable->subCategories && count($enrollment->enrollable->subCategories) > 0)
                                <div class="flex items-start">
                                    <i class="fas fa-tags w-5 text-purple-600 mr-3 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500 mb-2">Topics</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($enrollment->enrollable->subCategories as $subCategory)
                                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                                    {{ $subCategory->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                <!-- Right Column -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-credit-card text-purple-600 mr-2"></i>
                        Payment Information
                    </h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign w-5 text-green-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Amount Paid</p>
                                    <p class="font-medium text-green-600 text-lg">${{ number_format($enrollment->amount, 2) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <i class="fas fa-credit-card w-5 text-blue-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Payment Method</p>
                                    <p class="font-medium">{{ ucfirst($enrollment->payment_method ?? 'Card') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <i class="fas fa-check-circle w-5 text-green-600 mr-3"></i>
                                <div>
                                    <p class="text-sm text-gray-500">Payment Status</p>
                                    <p class="font-medium text-green-600">{{ ucfirst($enrollment->payment_status) }}</p>
                                </div>
                            </div>

                            @if($enrollment->enrolled_at)
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-plus w-5 text-purple-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Booked On</p>
                                        <p class="font-medium">{{ $enrollment->enrolled_at->format('M d, Y H:i A') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($enrollment->paymentTransaction)
                                <div class="flex items-center">
                                    <i class="fas fa-receipt w-5 text-gray-600 mr-3"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Transaction ID</p>
                                        <p class="font-medium text-xs">{{ $enrollment->paymentTransaction->transaction_id }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            <!-- Action Buttons -->
            <div class="bg-gray-50 rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt text-purple-600 mr-2"></i>
                    Quick Actions
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @if($enrollment->enrollment_status === 'active' && $enrollment->enrollable->date && $enrollment->enrollable->date->isFuture())
                        <button class="inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-sm">
                            <i class="fas fa-video mr-2"></i>
                            Join Session
                        </button>
                    @endif

                    <a href="{{ $conversation ? route('chat.show', $conversation->unique_code) : route('chat.index') }}" 
                       class="inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                        <i class="fas fa-comment mr-2"></i>
                        Message Mentor
                    </a>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
