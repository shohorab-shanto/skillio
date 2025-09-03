@extends('backend.layouts.app')

@section('title', __('trans.earning_history'))

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('trans.earning_history') }}</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="container">
    <!-- Earning Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Earning Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('trans.total_earning') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalEarning, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- This Month Earning Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-calendar text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('trans.this_month') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($thisMonthEarning, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Today's Earning Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-clock text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('trans.today') }}</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($todayEarning, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Card with Title and Search -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4">
            <h1 class="text-lg font-medium text-gray-900 mb-4 lg:mb-0">{{ __('trans.transaction') }}</h1>
            
            <!-- Search and Date Range -->
            <div class="w-full">
                <!-- Single Form for All Filters -->
                <form method="GET" action="{{ route('mentor.earnings') }}" class="space-y-4 lg:space-y-0">
                    <!-- Desktop Layout -->
                    <div class="hidden lg:flex items-center space-x-3">
                        <!-- Search Bar -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="{{ __('trans.search_transaction_id_date') }}" 
                                class="w-48 pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300 text-sm"
                            >
                        </div>
                        
                        <!-- Date Range Inputs -->
                        <div class="flex items-center space-x-2">
                            <input 
                                type="date" 
                                name="start_date"
                                value="{{ request('start_date') }}"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300"
                            >
                            <span class="text-gray-500 text-sm">{{ __('trans.to') }}</span>
                            <input 
                                type="date" 
                                name="end_date"
                                value="{{ request('end_date') }}"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300"
                            >
                        </div>
                        
                        <!-- Filter Button -->
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium text-sm"
                        >
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L6.293 13H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                            {{ __('trans.filter') }}
                        </button>
                        
                        @if(request('start_date') || request('end_date') || request('search'))
                            <a href="{{ route('mentor.earnings') }}" 
                               class="px-3 py-2 text-sm bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200 font-medium">
                                {{ __('trans.clear') }}
                            </a>
                        @endif
                    </div>

                    <!-- Mobile Layout -->
                    <div class="lg:hidden space-y-3">
                        <!-- Search Bar -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}"
                                placeholder="{{ __('trans.search_transaction_id_date') }}" 
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300 text-sm"
                            >
                        </div>
                        
                        <!-- Date Range Inputs -->
                        <div class="flex items-center space-x-2">
                            <input 
                                type="date" 
                                name="start_date"
                                value="{{ request('start_date') }}"
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300"
                            >
                            <span class="text-gray-500 text-sm">{{ __('trans.to') }}</span>
                            <input 
                                type="date" 
                                name="end_date"
                                value="{{ request('end_date') }}"
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300"
                            >
                        </div>
                        
                        <!-- Filter Buttons -->
                        <div class="flex space-x-2">
                            <button 
                                type="submit"
                                class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium text-sm"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L6.293 13H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                                {{ __('trans.filter') }}
                            </button>
                            
                            @if(request('start_date') || request('end_date') || request('search'))
                                <a href="{{ route('mentor.earnings') }}" 
                                   class="px-4 py-2 text-sm bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200 font-medium">
                                    {{ __('trans.clear') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-white rounded-lg shadow">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 tracking-wider">{{ __('trans.transaction_id') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 tracking-wider">{{ __('trans.date') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 tracking-wider">{{ __('trans.service_type') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 tracking-wider">{{ __('trans.title') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 tracking-wider">{{ __('trans.earning_amount') }}</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 tracking-wider">{{ __('trans.status') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        @php
                            // Get the first enrollment for this transaction (should be only one per transaction)
                            $enrollment = $payment->enrollments->first();
                            $enrollable = $enrollment ? $enrollment->enrollable : null;
                            $student = $enrollment ? $enrollment->user : null;
                            
                            // Get mentor name for courses
                            $mentorName = null;
                            if ($enrollable && $enrollment->enrollable_type == 'App\Models\Course') {
                                $mentorName = $enrollable->mentor->user->name ?? null;
                            }
                        @endphp
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->transaction_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->created_at->format('F j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($enrollable)
                                    @if($enrollment->enrollable_type == 'App\Models\Course')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ __('trans.course') }}
                                        </span>
                                    @elseif($enrollment->enrollable_type == 'App\Models\SessionBooking')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            {{ __('trans.session') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                            {{ class_basename($enrollment->enrollable_type) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($enrollable)
                                    <div>
                                        @if($enrollment->enrollable_type == 'App\Models\SessionBooking')
                                            <div class="font-medium text-gray-900">
                                                {{ __('trans.session_with') }} {{ $student ? $student->name : __('trans.unknown_student') }}
                                            </div>
                                        @else
                                            <div class="font-medium text-gray-900">
                                                {{ $enrollable->title ?? __('trans.untitled') }}
                                            </div>
                                            @if($mentorName)
                                                <div class="text-sm text-gray-500">
                                                    {{ __('trans.by') }} {{ $mentorName }}
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($payment->mentor_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->transaction_status == 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1.5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('trans.paid') }}
                                    </span>
                                @elseif($payment->transaction_status == 'failed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1.5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('trans.failed') }}
                                    </span>
                                @elseif($payment->transaction_status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('trans.pending') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($payment->transaction_status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('trans.no_earnings_found') }}</h3>
                                    <p class="text-gray-500">
                                        @if(request('start_date') || request('end_date') || request('search'))
                                            {{ __('trans.no_earnings_criteria') }}
                                        @else
                                            {{ __('trans.no_earnings_yet') }}
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Grid View -->
        <div class="lg:hidden space-y-4 p-4">
            @forelse($payments as $payment)
                @php
                    // Get the first enrollment for this transaction (should be only one per transaction)
                    $enrollment = $payment->enrollments->first();
                    $enrollable = $enrollment ? $enrollment->enrollable : null;
                    $student = $enrollment ? $enrollment->user : null;
                    
                    // Get mentor name for courses
                    $mentorName = null;
                    if ($enrollable && $enrollment->enrollable_type == 'App\Models\Course') {
                        $mentorName = $enrollable->mentor->user->name ?? null;
                    }
                @endphp
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-base font-medium text-gray-900">{{ $payment->transaction_id }}</p>
                            <p class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600">${{ number_format($payment->mentor_amount, 2) }}</p>
                            @if($payment->transaction_status == 'completed')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1.5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('trans.paid') }}
                                </span>
                            @elseif($payment->transaction_status == 'failed')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1.5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('trans.failed') }}
                                </span>
                            @elseif($payment->transaction_status == 'pending')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-1.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('trans.pending') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($payment->transaction_status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="space-y-2 mb-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">{{ __('trans.service_type') }}:</span>
                            <span class="text-sm font-medium text-gray-900">
                                @if($enrollable)
                                    @if($enrollment->enrollable_type == 'App\Models\Course')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ __('trans.course') }}
                                        </span>
                                    @elseif($enrollment->enrollable_type == 'App\Models\SessionBooking')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            {{ __('trans.session') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                            {{ class_basename($enrollment->enrollable_type) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-start">
                            <span class="text-sm text-gray-500">{{ __('trans.title') }}:</span>
                            <div class="text-right flex-1 ml-2">
                                @if($enrollable)
                                    @if($enrollment->enrollable_type == 'App\Models\SessionBooking')
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ __('trans.session_with') }} {{ $student ? $student->name : __('trans.unknown_student') }}
                                        </div>
                                    @else
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $enrollable->title ?? __('trans.untitled') }}
                                        </div>
                                        @if($mentorName)
                                            <div class="text-sm text-gray-500">
                                                {{ __('trans.by') }} {{ $mentorName }}
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('trans.no_earnings_found') }}</h3>
                        <p class="text-gray-500">
                            @if(request('start_date') || request('end_date') || request('search'))
                                {{ __('trans.no_earnings_criteria') }}
                            @else
                                {{ __('trans.no_earnings_yet') }}
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                <x-custom-pagination :paginator="$payments->appends(request()->query())" />
            </div>
        @endif
    </div>
</div>
@endsection


