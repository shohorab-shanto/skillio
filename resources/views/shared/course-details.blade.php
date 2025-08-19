{{-- Variables: $course, $showEditButton (default: false), $showEarningTab (default: false) --}}
@php
    $showEditButton = $showEditButton ?? false;
    $showEarningTab = $showEarningTab ?? false;
@endphp

<div class="w-full">
    <div class="mt-5 mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Course Image -->
        <div class="p-3">
            @if($course->cover_photo)
                <img src="{{ asset('storage/' . $course->cover_photo) }}" alt="{{ $course->title }}" class="w-full h-[500px] object-cover rounded-xl">
            @else
                <div class="w-full h-[500px] bg-gradient-to-br from-purple-600 via-fuchsia-500 to-pink-500 flex items-center justify-center rounded-xl">
                    <svg class="w-32 h-32 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Course Info -->
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-2">{{ $course->title }}</h2>

            <div class="flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-600 space-x-6">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                        </svg>
                        <span>{{ rand(500, 999) }} Student{{ rand(500, 999) > 1 ? 's' : '' }}</span>
                    </div>
                    @if($course->duration_days)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $course->duration_days }} Days</span>
                    </div>
                    @endif
                </div>
                
                <div class="flex items-center">
                    @php
                        $averageRating = $course->averageRating() ?? 0;
                        $totalReviews = $course->totalReviews();
                    @endphp
                    @if($totalReviews > 0)
                    <div class="flex items-center text-sm">
                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="font-semibold text-gray-900">{{ number_format($averageRating, 1) }}</span>
                        <span class="text-gray-500 ml-1">({{ $totalReviews }} Reviews)</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center">
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-8 flex-1">
                    @if($course->start_date)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-gray-900">Start Date:</span>
                            <span>{{ $course->start_date->format('M d, Y') }}</span>
                        </div>
                    @endif
                    @if($course->end_date)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-gray-900">End Date:</span>
                            <span>{{ $course->end_date->format('M d, Y') }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-right ml-auto">
                    @if($course->discount > 0)
                        <div class="flex flex-col items-end">
                            <div class="flex items-center space-x-3">
                                <span class="text-xl font-semibold text-purple-700">
                                    ${{ number_format($course->discounted_price, 2) }}
                                </span>
                                <span class="text-lg text-gray-400 line-through">
                                    ${{ number_format($course->price, 2) }}
                                </span>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 mt-1">
                                {{ $course->discount }}% OFF
                            </span>
                        </div>
                    @else
                        <div class="text-xl font-semibold text-purple-700">${{ number_format($course->price, 2) }}</div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center text-sm text-gray-600 space-x-4">
                    @if($course->mentor)
                    <span class="flex items-center">
                        @php
                            $mentorPhotoPath = $course->mentor->photo
                                ? (Str::startsWith($course->mentor->photo, ['http://', 'https://', '/storage/']) 
                                    ? $course->mentor->photo 
                                    : Storage::url($course->mentor->photo))
                                : asset('assets/images/user-avatar.png');
                        @endphp
                        <img src="{{ $mentorPhotoPath }}" alt="{{ $course->mentor->user->name }}" class="w-7 h-7 rounded-full object-cover mr-2" loading="lazy">
                        <span class="font-medium text-black">{{ $course->mentor->user->name }}</span>
                    </span>
                    @endif
                    <span>| {{ $course->category->name }}
                    @if($course->subCategories->count() > 0)
                        ·
                        @foreach($course->subCategories->take(2) as $subCategory)
                            {{ $subCategory->name }}{{ !$loop->last ? ' ·' : '' }}
                        @endforeach
                    @endif
                    </span>
                </div>
            </div>

            <!-- Action Button for Frontend -->
            @if(!$showEditButton)
            <div class="mb-6">
                @if(isset($isEnrolled) && $isEnrolled)
                    <div class="bg-green-100 text-green-800 px-8 py-3 rounded-lg text-lg font-medium inline-block border border-green-300">
                        ✓ Already Enrolled
                    </div>
                @else
                    <a href="{{ route('checkout.course', $course) }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg text-lg font-medium transition-colors inline-block">
                        Enroll Now ${{ number_format($course->discount > 0 ? $course->discounted_price : $course->price, 2) }}
                    </a>
                @endif
            </div>
            @endif

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('about')" id="about-tab"
                        class="tab-button text-purple-600 border-purple-600 py-4 px-1 border-b-2 text-sm font-medium">About</button>
                    <button onclick="showTab('review')" id="review-tab"
                        class="tab-button text-gray-500 hover:text-gray-700 py-4 px-1 border-b-2 border-transparent text-sm font-medium">Reviews</button>
                    @if($showEarningTab)
                    <button onclick="showTab('earning')" id="earning-tab"
                        class="tab-button text-gray-500 hover:text-gray-700 py-4 px-1 border-b-2 border-transparent text-sm font-medium">Earning History</button>
                    @endif
                </nav>
            </div>

            <!-- Tab Content -->
            <div id="tab-content">
                
                <!-- About Tab -->
                <div id="about-content" class="tab-content">
                    <div class="max-w-none">
                        <h3 class="text-lg font-semibold mb-4">Description</h3>
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $course->description }}</div>
                        
                        @if($course->subCategories->count() > 0)
                        <h3 class="text-lg font-semibold mt-6 mb-4">What You'll Learn</h3>
                        <ul class="space-y-2">
                            @foreach($course->subCategories as $subCategory)
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $subCategory->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                <!-- Review Tab -->
                <div id="review-content" class="tab-content hidden">
                    <h3 class="text-lg font-semibold mb-6">Student Reviews</h3>
                    
                    @if($course->reviews->count() > 0)
                        <!-- Review Statistics -->
                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                            <h4 class="text-md font-semibold mb-4">Review Statistics</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Overall Rating -->
                                <div class="text-center">
                                    <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($averageRating, 1) }}</div>
                                    <div class="flex justify-center items-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="text-gray-600">Based on {{ $course->reviews->count() }} reviews</p>
                                </div>
                                
                                <!-- Rating Breakdown -->
                                <div class="space-y-2">
                                    @for($star = 5; $star >= 1; $star--)
                                        @php
                                            $count = $course->reviews->where('rating', $star)->count();
                                            $percentage = $course->reviews->count() > 0 ? ($count / $course->reviews->count()) * 100 : 0;
                                        @endphp
                                        <div class="flex items-center space-x-3">
                                            <span class="text-sm font-medium text-gray-700 w-8">{{ $star }} ⭐</span>
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 w-8">{{ $count }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Individual Reviews -->
                        <h4 class="text-md font-semibold mb-4">Recent Reviews</h4>
                        <div class="space-y-4">
                            @foreach($course->reviews->take(5) as $review)
                                <div class="border-b border-gray-200 pb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $review->user->name ?? 'Anonymous Student' }}</p>
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endfor
                                                    <span class="text-sm text-gray-500 ml-2">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            <p class="text-gray-500">No reviews yet. Be the first to share your experience!</p>
                        </div>
                    @endif

                    <!-- Review Form for Enrolled Users -->
                    @if(auth()->check() && isset($isEnrolled) && $isEnrolled)
                        @php
                            $existingReview = auth()->user()->reviews()->where('course_id', $course->id)->first();
                        @endphp
                        @include('components.review-form', [
                            'type' => 'course',
                            'item' => $course,
                            'existingReview' => $existingReview
                        ])
                    @endif
                </div>

                @if($showEarningTab)
                <!-- Earning Tab -->
                <div id="earning-content" class="tab-content hidden">
                    <section class="max-w-[1280px] mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-5 gap-y-8 justify-items-center mt-5">
                        {{-- card 1 --}}
                        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xl text-gray-500">Total Earning</span>
                            </div>
                            <p class="text-5xl font-extrabold mt-3">$0</p>
                        </div>
                        {{-- card 2 --}}
                        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                    </svg>
                                </div>
                                <span class="text-xl text-gray-500">Total Enrolled Students</span>
                            </div>
                            <p class="text-5xl font-extrabold mt-3">0</p>
                        </div>
                        {{-- card 3 --}}
                        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1 1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xl text-gray-500">Active Sessions</span>
                            </div>
                            <p class="text-5xl font-extrabold mt-3">0</p>
                        </div>
                    </section>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
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

// Initialize default tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('about');
});
</script>
