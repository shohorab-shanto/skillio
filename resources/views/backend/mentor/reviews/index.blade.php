@extends('backend.layouts.app')

@section('title', 'My Reviews')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('mentor.profile.show') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Reviews</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    
    <!-- Rating Overview Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Rating Overview</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Overall Rating -->
            <div class="text-center p-6 bg-gray-50 rounded-lg">
                <div class="text-4xl font-bold text-gray-900 mb-2">{{ $stats['formatted_average'] }}</div>
                <div class="flex items-center justify-center mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $stats['star_rating']['full_stars'])
                            <i class="fa-solid fa-star text-yellow-400"></i>
                        @elseif($i == $stats['star_rating']['full_stars'] + 1 && $stats['star_rating']['half_star'])
                            <i class="fa-solid fa-star-half-stroke text-yellow-400"></i>
                        @else
                            <i class="fa-regular fa-star text-gray-300"></i>
                        @endif
                    @endfor
                </div>
                <p class="text-sm text-gray-600">{{ $stats['total_reviews'] }} {{ Str::plural('Review', $stats['total_reviews']) }}</p>
                
                @if($stats['has_excellent_reviews'])
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                        <i class="fa-solid fa-badge-check mr-1"></i>
                        Excellent Reviews
                    </span>
                @endif
            </div>

            <!-- Rating Distribution -->
            <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Rating Distribution</h3>
                @for($rating = 5; $rating >= 1; $rating--)
                    @php
                        $count = $stats['rating_distribution'][$rating] ?? 0;
                        $percentage = $stats['total_reviews'] > 0 ? ($count / $stats['total_reviews']) * 100 : 0;
                    @endphp
                    <div class="flex items-center mb-3">
                        <span class="text-sm text-gray-600 w-12">{{ $rating }} star</span>
                        <div class="flex-1 mx-4 bg-gray-200 rounded-full h-3">
                            <div class="bg-yellow-400 h-3 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-16">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['five_star_percentage'] }}%</div>
                <p class="text-sm text-gray-600">5-Star Reviews</p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['rating_distribution'][4] ?? 0 }}</div>
                <p class="text-sm text-gray-600">4-Star Reviews</p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ ($stats['rating_distribution'][3] ?? 0) + ($stats['rating_distribution'][2] ?? 0) + ($stats['rating_distribution'][1] ?? 0) }}</div>
                <p class="text-sm text-gray-600">Below 4-Star</p>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['total_reviews'] }}</div>
                <p class="text-sm text-gray-600">Total Reviews</p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="GET" action="{{ route('mentor.reviews.index') }}" class="flex flex-wrap items-center gap-4">
            
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $request->search }}" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm" 
                           placeholder="Search reviews by comment or student name...">
                </div>
            </div>

            <!-- Rating Filter -->
            <div>
                <select name="rating" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">All Ratings</option>
                    <option value="5" {{ $request->rating == '5' ? 'selected' : '' }}>5 Stars</option>
                    <option value="4" {{ $request->rating == '4' ? 'selected' : '' }}>4 Stars</option>
                    <option value="3" {{ $request->rating == '3' ? 'selected' : '' }}>3 Stars</option>
                    <option value="2" {{ $request->rating == '2' ? 'selected' : '' }}>2 Stars</option>
                    <option value="1" {{ $request->rating == '1' ? 'selected' : '' }}>1 Star</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center space-x-2">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-filter mr-2"></i>
                    Filter
                </button>
                
                <a href="{{ route('mentor.reviews.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fa-solid fa-refresh mr-2"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Reviews List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">
                    Student Reviews 
                    @if($request->rating)
                        <span class="text-sm font-normal text-gray-500">({{ $request->rating }}-star reviews)</span>
                    @endif
                    @if($request->search)
                        <span class="text-sm font-normal text-gray-500">(searching "{{ $request->search }}")</span>
                    @endif
                </div>
                <div class="text-sm text-gray-500">
                    Showing {{ $reviews->firstItem() ?? 0 }}-{{ $reviews->lastItem() ?? 0 }} of {{ $reviews->total() }} reviews
                </div>
            </div>
        </div>

        @if($reviews->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($reviews as $review)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <!-- Student Avatar -->
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                                    {{ substr($review->user->name, 0, 2) }}
                                </div>
                                
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $review->user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $review->user->email }}</p>
                                    
                                    <!-- Rating -->
                                    <div class="flex items-center space-x-1 mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fa-solid fa-star text-yellow-400 text-sm"></i>
                                            @else
                                                <i class="fa-regular fa-star text-gray-300 text-sm"></i>
                                            @endif
                                        @endfor
                                        <span class="text-sm text-gray-600 ml-2">{{ $review->rating }}/5</span>
                                        
                                        @if($review->rating >= 5)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                                                Excellent
                                            </span>
                                        @elseif($review->rating >= 4)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                                Good
                                            </span>
                                        @elseif($review->rating >= 3)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                                Average
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                                Needs Improvement
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Date -->
                            <div class="text-right">
                                <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- Review Comment -->
                        <div class="ml-15">
                            <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                            
                            @if($review->course)
                                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">
                                        <i class="fa-solid fa-book mr-1"></i>
                                        Review for course: <span class="font-medium">{{ $review->course->title }}</span>
                                    </p>
                                </div>
                            @else
                                <div class="mt-3 p-3 bg-purple-50 rounded-lg">
                                    <p class="text-sm text-purple-700">
                                        <i class="fa-solid fa-user-graduate mr-1"></i>
                                        Mentoring review
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($reviews->hasPages())
                <div class="p-6 border-t border-gray-200">
                    {{ $reviews->appends(request()->query())->links() }}
                </div>
            @endif

        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-star text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    @if($request->search || $request->rating)
                        No reviews found
                    @else
                        No reviews yet
                    @endif
                </h3>
                <p class="text-gray-500 mb-6">
                    @if($request->search || $request->rating)
                        Try adjusting your search criteria or filters.
                    @else
                        Your student reviews will appear here once they start rating your mentoring.
                    @endif
                </p>
                
                @if($request->search || $request->rating)
                    <a href="{{ route('mentor.reviews.index') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        View All Reviews
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
