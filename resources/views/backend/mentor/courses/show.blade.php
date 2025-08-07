@extends('backend.layouts.app')

@section('title', 'Course Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('mentor.courses.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Course Details</h1>
                <p class="text-sm text-gray-500 mt-1">View your course information</p>
            </div>
        </div>
        @if($course->needs_reapproval)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-300 shadow-sm ml-4">
            <i class="fa-solid fa-circle-exclamation mr-2 text-orange-500"></i>
            Re-Approval Required
            </span>
        @endif
        
        <!-- Edit Button -->
        <div class="flex items-center space-x-3">
            <a href="{{ route('mentor.courses.edit', $course) }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fa-solid fa-edit mr-2"></i>
                Edit Course
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="w-full">
    <div class="mt-5 mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Course Image -->
        @if($course->cover_photo)
            <img src="{{ asset('storage/' . $course->cover_photo) }}" alt="{{ $course->title }}" class="w-full h-[500px] object-cover">
        @else
            <div class="w-full h-[500px] bg-gradient-to-br from-purple-600 via-blue-600 to-purple-800 flex items-center justify-center">
                <i class="fa-solid fa-graduation-cap text-8xl text-white/50"></i>
            </div>
        @endif

        <!-- Course Info -->
        <div class="p-6">
            <h2 class="text-2xl font-bold mb-2">{{ $course->title }}</h2>

            <div class="flex items-center justify-between">
                <div class="flex items-center text-sm text-gray-600 space-x-6">
                    <div class="flex items-center">
                        <i class="fa-solid fa-users mr-2 text-gray"></i>
                        <span>{{ rand(500, 999) }} Student{{ rand(500, 999) > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fa-solid fa-clock mr-2 text-gray"></i>
                        <span>{{ $course->duration_days }} Days</span>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="flex items-center text-sm">
                        <i class="fa-solid fa-star text-yellow-400 mr-1"></i>
                        <span class="font-semibold text-gray-900">{{ number_format($course->reviews->avg('rating') ?? 4.5, 1) }}</span>
                        <span class="text-gray-500 ml-1">({{ $course->reviews->count() > 0 ? $course->reviews->count() : rand(500, 999) }} Reviews)</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center">
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-8 flex-1">
                    @if($course->start_date)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-gray-900">Start Date:</span>
                            <span>{{ \Carbon\Carbon::parse($course->start_date)->format('M d, Y') }}</span>
                        </div>
                    @endif
                    @if($course->end_date)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-gray-900">End Date:</span>
                            <span>{{ \Carbon\Carbon::parse($course->end_date)->format('M d, Y') }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-right ml-auto">
                    @if($course->discount > 0)
                        <div class="flex flex-col items-end">
                            <div class="flex items-center space-x-3">
                                <span class="text-xl font-semibold text-purple-700">
                                    ${{ number_format($course->price * (1 - $course->discount / 100), 2) }}
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
                    <span class="flex items-center">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}" class="w-7 h-7 rounded-full object-cover mr-2">
                        @else
                            <span class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center mr-2">
                                <i class="fa-solid fa-user text-purple-600"></i>
                            </span>
                        @endif
                        <span class="font-medium text-black">{{ auth()->user()->name }}</span>
                    </span>
                    <span>|  {{ $course->category->name }} .
                    @foreach($course->subCategories->take(2) as $subCategory)
                        {{ $subCategory->name }}{{ !$loop->last ? ' ·' : '' }}
                    @endforeach
                    </span>
                </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('about')" id="about-tab"
                        class="tab-button text-purple-600 border-purple-600 py-4 px-1 border-b-2 text-sm font-medium">About</button>
                    <button onclick="showTab('review')" id="review-tab"
                        class="tab-button text-gray-500 hover:text-gray-700 py-4 px-1 border-b-2 border-transparent text-sm font-medium">Review</button>
                    <button onclick="showTab('earning')" id="earning-tab"
                        class="tab-button text-gray-500 hover:text-gray-700 py-4 px-1 border-b-2 border-transparent text-sm font-medium">Earning History</button>
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
                                    <i class="fa-solid fa-check text-green-500 mr-3"></i>
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
                                    <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($course->reviews->avg('rating') ?? 4.5, 1) }}</div>
                                    <div class="flex justify-center items-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star text-lg {{ $i <= round($course->reviews->avg('rating') ?? 4.5) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
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
                                                <i class="fa-solid fa-user text-purple-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $review->user->name ?? 'Anonymous Student' }}</p>
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fa-solid fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
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
                            <i class="fa-solid fa-star-half-stroke text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No reviews yet. Be the first to get feedback from students!</p>
                        </div>
                    @endif
                </div>

                <!-- Earning Tab -->
                <div id="earning-content" class="tab-content hidden">
                    <section class="max-w-[1280px] mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-5 gap-y-8 justify-items-center mt-5">
                        {{-- card 1 --}}
                        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-dollar-sign text-green-600"></i>
                                </div>
                                <span class="text-xl text-gray-500">Total Earning</span>
                            </div>
                            <p class="text-5xl font-extrabold mt-3">$0</p>
                        </div>
                        {{-- card 2 --}}
                        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-users text-blue-600"></i>
                                </div>
                                <span class="text-xl text-gray-500">Total Enrolled Students</span>
                            </div>
                            <p class="text-5xl font-extrabold mt-3">0</p>
                        </div>
                        {{-- card 3 --}}
                        <div class="card bg-base-100 w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-calendar text-purple-600"></i>
                                </div>
                                <span class="text-xl text-gray-500">Active Sessions</span>
                            </div>
                            <p class="text-5xl font-extrabold mt-3">0</p>
                        </div>
                    </section>
                </div>

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

@endsection
