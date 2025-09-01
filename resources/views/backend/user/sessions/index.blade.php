@extends('backend.layouts.app')

@section('title', __('trans.my_sessions'))

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('trans.my_sessions') }}</h1>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('mentors') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-plus mr-2"></i>
                {{ __('trans.book_new_session') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-video text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('trans.total_sessions') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('trans.upcoming') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_sessions'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">{{ __('trans.completed_sessions') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_sessions'] }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('user.sessions') }}" class="flex flex-wrap items-center gap-4">
            
            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('trans.from_date') }}</label>
                <input type="date" name="date_from" value="{{ $request->date_from }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('trans.to_date') }}</label>
                <input type="date" name="date_to" value="{{ $request->date_to }}" 
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('trans.status') }}</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900 cursor-pointer hover:border-gray-400 transition-colors appearance-none w-40">
                    <option value="">{{ __('trans.all_sessions') }}</option>
                    <option value="upcoming" {{ $request->status == 'upcoming' ? 'selected' : '' }}>{{ __('trans.upcoming') }}</option>
                    <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>{{ __('trans.completed') }}</option>
                    <option value="past" {{ $request->status == 'past' ? 'selected' : '' }}>{{ __('trans.past') }}</option>
                </select>
            </div>

            <!-- Mentor Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('trans.mentor_name') }}</label>
                <input type="text" name="mentor" value="{{ $request->mentor }}" placeholder="{{ __('trans.search_mentor') }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-col">
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                <div class="flex items-center space-x-2">
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-filter mr-2"></i>
                        {{ __('trans.filter') }}
                    </button>
                    
                    <a href="{{ route('user.sessions') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-refresh mr-2"></i>
                        {{ __('trans.refresh') }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Sessions Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        @if($enrollments->count() > 0)
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ __('trans.your_sessions') }}
                        @if($request->status)
                            <span class="text-sm font-normal text-gray-500">({{ ucfirst($request->status) }} {{ __('trans.sessions') }})</span>
                        @endif
                        @if($request->date_from || $request->date_to)
                            <span class="text-sm font-normal text-gray-500">
                                ({{ $request->date_from ? \Carbon\Carbon::parse($request->date_from)->format('M d') : 'All' }} - {{ $request->date_to ? \Carbon\Carbon::parse($request->date_to)->format('M d, Y') : 'All' }})
                            </span>
                        @endif
                    </h2>
                    <div class="text-sm text-gray-500">
                        {{ __('trans.showing') }} {{ $enrollments->firstItem() ?? 0 }}-{{ $enrollments->lastItem() ?? 0 }} {{ __('trans.of') }} {{ $enrollments->total() }} {{ __('trans.sessions') }}
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enrollments as $enrollment)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all duration-300">
                        <!-- Header with Day and Actions -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                <h3 class="font-semibold text-gray-900">
                                    @if($enrollment->enrollable->date)
                                        {{ $enrollment->enrollable->date->format('l, j F Y') }}
                                    @else
                                        {{ __('trans.session') }}
                                    @endif
                                </h3>
                            </div>

                        </div>

                        <!-- Time Range -->
                        <div class="mb-4">
                            <h4 class="text-xl font-bold text-gray-900 mb-2">
                                @if($enrollment->enrollable->start_time && $enrollment->enrollable->end_time)
                                    {{ $enrollment->enrollable->start_time->format('g:i A') }} - 
                                    {{ $enrollment->enrollable->end_time->format('g:i A') }}
                                @else
                                    {{ __('trans.time_tbd') }}
                                @endif
                            </h4>
                        </div>

                        <!-- Mentor Info -->
                        <div class="mb-4">
                            <div class="flex items-center space-x-3">
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
                                         class="w-8 h-8 rounded-full object-cover">
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $enrollment->enrollable->mentor->user->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Categories/Subjects -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                <!-- Main Category -->
                                @if($enrollment->enrollable->category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $enrollment->enrollable->category->name }}
                                    </span>
                                @endif
                                
                                <!-- Sub Categories -->
                                @foreach($enrollment->enrollable->subCategories as $subCategory)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $subCategory->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span>
                                @if($enrollment->enrollable->start_time && $enrollment->enrollable->end_time)
                                    {{ $enrollment->enrollable->start_time->diffInMinutes($enrollment->enrollable->end_time) }} {{ __('trans.mins') }}
                                @else
                                    {{ __('trans.duration_tbd') }}
                                @endif
                            </span>
                            
                            @if($enrollment->enrollment_status == 'active')
                                @if($enrollment->enrollable->date && $enrollment->enrollable->date->isFuture())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                        <i class="fa-solid fa-clock mr-1"></i>
                                        {{ __('trans.upcoming_badge') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                                        <i class="fa-solid fa-check mr-1"></i>
                                        {{ __('trans.active_badge') }}
                                    </span>
                                @endif
                            @elseif($enrollment->enrollment_status == 'completed')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                                    <i class="fa-solid fa-check-circle mr-1"></i>
                                    {{ __('trans.completed_badge') }}
                                </span>
                            @elseif($enrollment->enrollment_status == 'cancelled')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                                    <i class="fa-solid fa-times mr-1"></i>
                                    {{ __('trans.cancelled_badge') }}
                                </span>
                            @endif
                        </div>



                        <!-- Action Buttons -->
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-center mb-3">
                                @if($enrollment->enrollment_status == 'active' && $enrollment->enrollable->date && $enrollment->enrollable->date->isFuture())
                                    <span class="text-green-600 hover:text-green-700 text-sm font-medium">
                                        <i class="fa-solid fa-video mr-1"></i>
                                        {{ __('trans.join_session') }}
                                    </span>
                                   @else
                                    <span class="text-gray-400 text-sm">
                                        @if($enrollment->enrolled_at)
                                            {{ __('trans.booked') }} {{ $enrollment->enrolled_at->diffForHumans() }}
                                        @else
                                            {{ __('trans.session_enrolled') }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Chat and Details Buttons -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ $enrollment->conversation ? route('chat.show', $enrollment->conversation->unique_code) : route('chat.index') }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                                   title="{{ __('trans.chat_with_mentor') }}">
                                    <i class="fa-solid fa-comment text-sm mr-2"></i>
                                    {{ __('trans.chat') }}
                                </a>
                                <a href="{{ route('user.sessions.show', $enrollment->id) }}" 
                                   class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors"
                                   title="{{ __('trans.view_details') }}">
                                    <i class="fa-solid fa-eye text-sm mr-2"></i>
                                    {{ __('trans.details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            </div>

            <!-- Pagination -->
            @if($enrollments->hasPages())
                <div class="p-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if ($enrollments->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                    {{ __('trans.previous') }}
                                </span>
                            @else
                                <a href="{{ $enrollments->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    {{ __('trans.previous') }}
                                </a>
                            @endif

                            @if ($enrollments->hasMorePages())
                                <a href="{{ $enrollments->appends(request()->query())->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    {{ __('trans.next') }}
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">
                                    {{ __('trans.next') }}
                                </span>
                            @endif
                        </div>

                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 leading-5">
                                    {{ __('trans.showing') }}
                                    <span class="font-medium">{{ $enrollments->firstItem() ?? 0 }}</span>
                                    {{ __('trans.to') }}
                                    <span class="font-medium">{{ $enrollments->lastItem() ?? 0 }}</span>
                                    {{ __('trans.of') }}
                                    <span class="font-medium">{{ $enrollments->total() }}</span>
                                    {{ __('trans.sessions') }}
                                </p>
                            </div>

                            <div>
                                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                                    {{-- Previous Page Link --}}
                                    @if ($enrollments->onFirstPage())
                                        <span aria-disabled="true" aria-label="Previous">
                                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                                <i class="fa-solid fa-chevron-left"></i>
                                            </span>
                                        </span>
                                    @else
                                        <a href="{{ $enrollments->appends(request()->query())->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Previous">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </a>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($enrollments->getUrlRange(1, $enrollments->lastPage()) as $page => $url)
                                        @if ($page == $enrollments->currentPage())
                                            <span aria-current="page">
                                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-bold text-white bg-purple-600 border border-purple-600 cursor-default leading-5">{{ $page }}</span>
                                            </span>
                                        @else
                                            <a href="{{ $enrollments->appends(request()->query())->url($page) }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-purple-600 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($enrollments->hasMorePages())
                                        <a href="{{ $enrollments->appends(request()->query())->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 hover:bg-purple-50 focus:z-10 focus:outline-none focus:ring ring-purple-300 focus:border-purple-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="Next">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    @else
                                        <span aria-disabled="true" aria-label="Next">
                                            <span class="relative inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-gray-400 bg-gray-50 border border-gray-300 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                                <i class="fa-solid fa-chevron-right"></i>
                                            </span>
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-video text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if($request->date_from || $request->date_to || $request->status || $request->mentor)
                        {{ __('trans.no_sessions_found_title') }}
                    @else
                        {{ __('trans.no_sessions_yet_title') }}
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if($request->date_from || $request->date_to || $request->status || $request->mentor)
                        {{ __('trans.try_adjusting_filters') }}
                    @else
                        {{ __('trans.start_booking_sessions') }}
                    @endif
                </p>
                
                @if($request->date_from || $request->date_to || $request->status || $request->mentor)
                    <a href="{{ route('user.sessions') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        {{ __('trans.view_all_sessions') }}
                    </a>
                @else
                    <a href="{{ route('mentors') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-2"></i>
                        {{ __('trans.book_first_session') }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>


@endsection