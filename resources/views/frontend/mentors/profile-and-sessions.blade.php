@extends('frontend.layouts.app')

@section('title', $mentorInfo['name'] . ' - Profile & Sessions')

@section('navbar-style')
    <style>
        #navbar {
            background-color: #f9fafb !important; /* Tailwind's bg-gray-50 */
            border-bottom: none !important;
            box-shadow: none !important;
        }
        
    </style>
@endsection

@section('content')
    <div class="min-h-screen bg-gray-50 pt-20">
        <div class="max-w-[1400px] mx-auto px-6">
            <section class="max-w-[1400px] mx-auto rounded-2xl p-6 bg-white">
                <div class="max-w-[1400px] mx-auto rounded-2xl p-6 md:p-10 grid md:grid-cols-3 gap-6 items-center">
                    <!-- Left: Image + Details -->
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <!-- Profile Image -->
                        <div class="w-[150px] h-[150px] rounded-full overflow-hidden border-2 border-blue-500 flex-shrink-0">
                            @php
                                $photoPath = $mentorInfo['photo']
                                    ? (Str::startsWith($mentorInfo['photo'], ['http://', 'https://', '/storage/']) 
                                        ? $mentorInfo['photo'] 
                                        : Storage::url($mentorInfo['photo']))
                                    : asset('assets/images/user-avatar.png');
                            @endphp
                            <img src="{{ $photoPath }}" alt="{{ $mentorInfo['name'] }}"
                                class="w-full h-full object-cover rounded-full" />
                        </div>

                        <!-- Text Info -->
                        <div class="text-center md:text-left space-y-6 min-w-0 flex-4 px-4 py-2">
                            <h2 class="text-xl font-bold text-gray-900">{{ $mentorInfo['name'] }}</h2>
                            <div class="flex items-center justify-center md:justify-start gap-2 text-sm text-gray-600">
                                @php
                                    $fullStars = $mentorInfo['star_rating']['full_stars'] ?? 0;
                                    $halfStar = $mentorInfo['star_rating']['half_star'] ?? 0;
                                    $emptyStars = $mentorInfo['star_rating']['empty_stars'] ?? 0;
                                @endphp
                                <span class="flex items-center gap-1 flex-wrap">
                                    @for($i = 0; $i < $fullStars; $i++)
                                        <svg class="w-4 h-4 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endfor
                                    @if($halfStar)
                                        <svg class="w-4 h-4 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                            <defs>
                                                <linearGradient id="half-grad">
                                                    <stop offset="50%" stop-color="currentColor"/>
                                                    <stop offset="50%" stop-color="#E5E7EB"/>
                                                </linearGradient>
                                            </defs>
                                            <path fill="url(#half-grad)" d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endif
                                    @for($i = 0; $i < $emptyStars; $i++)
                                        <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endfor
                                    <span class="font-semibold text-gray-900 ml-2">{{ $mentorInfo['formatted_rating'] }}</span>
                                    <span class="text-gray-400 ml-1">({{ $mentorInfo['total_reviews'] }} {{ Str::plural('Review', $mentorInfo['total_reviews']) }})</span>
                                </span>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm text-gray-500">Mentor</p>
                                <p class="font-semibold text-gray-900 break-words leading-tight">{{ $mentorInfo['top_category'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bio/Experience (center section) -->
                    <div class="flex flex-col justify-center h-full text-feft mt-10 md:mt-0">
                        <p class="text-sm text-gray-500 mb-1">Work Experience</p>
                        <p class="font-semibold text-gray-900">{{ $mentorInfo['experience_years'] }}</p>
                        <p class="text-sm text-gray-500 mb-1 mt-4">Bio</p>
                        <p class="font-semibold text-gray-900">{{ $mentorInfo['bio'] }}</p>
                    </div>
                    
                    <!-- Right: Online status and Review button -->
                    <div class="relative h-full flex flex-col items-end justify-between">
                        <!-- Online badge (top-right) -->
                        <div class="flex items-center gap-2 bg-gray-100 px-3 py-1 rounded-full text-sm">
                            <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                            <span class="text-gray-700">{{ ucfirst($mentorInfo['availability']) }}</span>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Categories Section -->
                <div class="px-6 flex flex-col gap-2">
                    <p class="text-sm text-gray-600 mb-2">Categories</p>
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex flex-wrap gap-3">
                            @if($mentorInfo['top_category_sub_categories'] && count($mentorInfo['top_category_sub_categories']) > 0)
                                @foreach($mentorInfo['top_category_sub_categories'] as $subCategory)
                                    <span class="px-6 py-2 bg-gray-100 text-gray-800 rounded-full whitespace-nowrap text-sm">{{ $subCategory }}</span>
                                @endforeach
                            @else
                                <span class="px-6 py-2 bg-gray-100 text-gray-800 rounded-full whitespace-nowrap text-sm">Expert</span>
                                <span class="px-6 py-2 bg-gray-100 text-gray-800 rounded-full whitespace-nowrap text-sm">Professional</span>
                                <span class="px-6 py-2 bg-gray-100 text-gray-800 rounded-full whitespace-nowrap text-sm">Verified</span>
                            @endif
                        </div>
                        <div class="ml-auto">
                            <button onclick="showTab('review')" class="block px-4 py-2 bg-purple-700 text-white rounded hover:bg-purple-800 transition-colors duration-300 text-center whitespace-nowrap">
                                Review
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <!-- Tab Navigation -->
            <section class="pt-5">
                <div class="max-w-[1200px] mx-auto bg-white rounded-2xl p-5">
                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <button onclick="showTab('timeslots')" id="timeslots-tab"
                                class="tab-button text-purple-600 border-purple-600 py-4 px-1 border-b-2 text-sm font-medium">Time Slots</button>
                            <button onclick="showTab('review')" id="review-tab"
                                class="tab-button text-gray-500 hover:text-gray-700 py-4 px-1 border-b-2 border-transparent text-sm font-medium">Reviews</button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div id="tab-content">
                        
                        <!-- Time Slots Tab -->
                        <div id="timeslots-content" class="tab-content">
                            <div class="flex justify-between items-center mb-6">
                                <!-- Left: Title -->
                                <h2 class="font-semibold text-gray-900">All Time Slots</h2>
                                
                                <!-- Right: Date Selection Dropdown -->
                                <div class="relative" x-data="{ open: false, selectedPeriod: 'Monthly' }">
                                    <button 
                                        @click="open = !open"
                                        class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-gray-700" x-text="selectedPeriod"></span>
                                        <svg class="w-4 h-4 text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown Menu -->
                                    <div 
                                        x-show="open" 
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10"
                                    >
                                        <!-- Date Range Inputs -->
                                        <form method="GET" action="{{ route('mentor.sessions', $mentorInfo['id']) }}" class="p-4">
                                            <div class="text-sm font-medium text-gray-700 mb-3">Select Date Range</div>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">From Date</label>
                                                    <input 
                                                        type="date" 
                                                        name="start_date"
                                                        value="{{ request('start_date') }}"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                    >
                                                </div>
                                                <div>
                                                    <label class="block text-xs text-gray-600 mb-1">To Date</label>
                                                    <input 
                                                        type="date" 
                                                        name="end_date"
                                                        value="{{ request('end_date') }}"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                    >
                                                </div>
                                                <button 
                                                    type="submit"
                                                    class="w-full px-4 py-2 text-sm bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium"
                                                >
                                                    Apply Filter
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sessions Grid inside Time Slots Tab -->
                            @if($activeSessions && $activeSessions->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                    @foreach($activeSessions as $session)
                                        @include('shared.mentor-session-card', ['session' => $session])
                                    @endforeach
                                </div>
                                
                                <!-- Pagination -->
                                @if($activeSessions->hasPages())
                                    <div class="mt-8 flex justify-center">
                                        {{ $activeSessions->appends(request()->query())->links() }}
                                    </div>
                                @endif
                            @else
                                <!-- No Active Sessions Message -->
                                <div class="text-center py-12">
                                    <div class="max-w-md mx-auto">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Active Sessions</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if(request('start_date') || request('end_date'))
                                                No sessions found for the selected date range.
                                            @else
                                                This mentor doesn't have any available time slots at the moment.
                                            @endif
                                        </p>
                                        @if(request('start_date') || request('end_date'))
                                            <div class="mt-4">
                                                <a href="{{ route('mentor.sessions', $mentorInfo['id']) }}" 
                                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-purple-600 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                    Clear Date Filter
                                                </a>
                                            </div>
                                        @else
                                            <div class="mt-6">
                                                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    Check Back Later
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Review Tab -->
                        <div id="review-content" class="tab-content hidden">
                            <h3 class="text-lg font-semibold mb-6">Mentor Reviews</h3>
                            
                            @php
                                $mentor = \App\Models\Mentor::find($mentorInfo['id']);
                                $mentorReviews = $mentor ? $mentor->reviews()->with('user')->orderBy('created_at', 'desc')->get() : collect();
                                $averageRating = $mentorReviews->count() > 0 ? $mentorReviews->avg('rating') : 0;
                                $totalReviews = $mentorReviews->count();
                            @endphp
                            
                            @if($mentorReviews->count() > 0)
                                <!-- Review Statistics -->
                                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                                    <h4 class="text-md font-semibold mb-4">Review Statistics</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Overall Rating -->
                                        <div class="text-center">
                                            <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($averageRating, 1) }}</div>
                                            <div class="flex justify-center mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($averageRating))
                                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @elseif($i == ceil($averageRating) && $averageRating - floor($averageRating) > 0)
                                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <defs>
                                                                <linearGradient id="half-star">
                                                                    <stop offset="50%" stop-color="currentColor"/>
                                                                    <stop offset="50%" stop-color="#E5E7EB"/>
                                                                </linearGradient>
                                                            </defs>
                                                            <path fill="url(#half-star)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                            <div class="text-sm text-gray-600">{{ $totalReviews }} {{ Str::plural('Review', $totalReviews) }}</div>
                                        </div>
                                        
                                        <!-- Rating Distribution -->
                                        <div class="space-y-3">
                                            @for($rating = 5; $rating >= 1; $rating--)
                                                @php
                                                    $count = $mentorReviews->where('rating', $rating)->count();
                                                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                                @endphp
                                                <div class="flex items-center text-sm">
                                                    <span class="w-3">{{ $rating }}</span>
                                                    <svg class="w-4 h-4 text-yellow-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    <div class="flex-1 mx-2">
                                                        <div class="bg-gray-200 rounded-full h-2">
                                                            <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                    </div>
                                                    <span class="w-8 text-right">{{ $count }}</span>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                                <!-- Individual Reviews -->
                                <div class="space-y-6">
                                    @foreach($mentorReviews as $review)
                                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                                            <div class="flex items-start justify-between mb-4">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                                        <span class="text-purple-600 font-semibold">{{ substr($review->user->name, 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-medium text-gray-900">{{ $review->user->name }}</h5>
                                                        <div class="flex items-center mt-1">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $review->rating)
                                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                    </svg>
                                                                @else
                                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                    </svg>
                                                                @endif
                                                            @endfor
                                                            <span class="ml-2 text-sm text-gray-600">{{ $review->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- No Reviews Message -->
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                    <p class="text-gray-500">No reviews yet. Be the first to share your experience!</p>
                                </div>
                            @endif

                            <!-- Review Form for Users who have had sessions with this mentor -->
                            @if(auth()->check())
                                @php
                                    $userHasBookedSession = \App\Models\UserEnrollment::where('user_id', auth()->id())
                                        ->where('enrollable_type', \App\Models\SessionBooking::class)
                                        ->whereHas('enrollable', function($query) use ($mentorInfo) {
                                            $query->where('mentor_id', $mentorInfo['id']);
                                        })
                                        ->exists();
                                    $existingReview = auth()->user()->reviews()->where('mentor_id', $mentorInfo['id'])->first();
                                @endphp
                                
                                @if($userHasBookedSession)
                                    @include('components.review-form', [
                                        'type' => 'mentor',
                                        'item' => $mentor,
                                        'existingReview' => $existingReview
                                    ])
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

<script>
    // Tab functionality
    function showTab(tabName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active styles from all tabs
        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
            button.classList.remove('text-purple-600', 'border-purple-600');
            button.classList.add('text-gray-500', 'border-transparent');
        });
        
        // Show selected tab content
        document.getElementById(tabName + '-content').classList.remove('hidden');
        
        // Add active styles to selected tab
        const activeTab = document.getElementById(tabName + '-tab');
        activeTab.classList.remove('text-gray-500', 'border-transparent');
        activeTab.classList.add('text-purple-600', 'border-purple-600');
    }

    // Initialize with current month or selected date range
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize default tab
        showTab('timeslots');
        
        const startDate = '{{ request("start_date") }}';
        const endDate = '{{ request("end_date") }}';
        
        if (startDate && endDate) {
            // Format dates for display
            const formatDate = (date) => new Date(date).toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric'
            });
            
            const selectedPeriod = `${formatDate(startDate)} - ${formatDate(endDate)}`;
            const alpineComponent = document.querySelector('[x-data]');
            if (alpineComponent && alpineComponent.__x) {
                alpineComponent.__x.$data.selectedPeriod = selectedPeriod;
            }
        } else {
            const today = new Date();
            const currentMonth = today.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
            const alpineComponent = document.querySelector('[x-data]');
            if (alpineComponent && alpineComponent.__x) {
                alpineComponent.__x.$data.selectedPeriod = currentMonth;
            }
        }
    });
</script>
