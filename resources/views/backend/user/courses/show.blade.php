@extends('backend.layouts.app')

@section('title', 'Course Details')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('user.courses') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Course Details</h1>
                <p class="text-sm text-gray-500 mt-1">View your enrolled course information</p>
            </div>
        </div>
        
        <!-- Status Badge -->
        <div class="flex items-center space-x-3">
            @if($enrollment->enrollment_status == 'active')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700 border border-green-300 shadow-sm">
                    <i class="fa-solid fa-play-circle mr-2 text-green-500"></i>
                    Active Course
                </span>
            @elseif($enrollment->enrollment_status == 'completed')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-700 border border-blue-300 shadow-sm">
                    <i class="fa-solid fa-check-circle mr-2 text-blue-500"></i>
                    Completed
                </span>
            @endif
        </div>
    </div>
@endsection

@section('content')
    @php
        $course = $enrollment->enrollable;
    @endphp

    <div class="space-y-8">
        <!-- Course Overview Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Course Image -->
            <div class="relative p-2">
                @if($course->cover_photo)
                    <img src="{{ asset('storage/' . $course->cover_photo) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded-lg">
                @elseif($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-64 object-cover rounded-lg">
                @else
                    <div class="w-full h-64 bg-gradient-to-br from-purple-600 via-fuchsia-500 to-pink-500 flex items-center justify-center rounded-lg">
                        <svg class="w-20 h-20 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v10l10 5 10-5V7L12 2zm0 2.31L18.6 7 12 9.69 5.4 7 12 4.31zM4 8.5l8 4 8-4V16l-8 4-8-4V8.5z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="p-6">
                <!-- Course Title & Description -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $course->title }}</h2>
                    @if($course->description)
                        <p class="text-gray-600 leading-relaxed">{{ $course->description }}</p>
                    @endif
                </div>

                <!-- Course Meta Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Category -->
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-tag text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Category</p>
                            <p class="font-semibold text-gray-900">{{ $course->category->name }}</p>
                        </div>
                    </div>

                    <!-- Duration -->
                    @if($course->duration_days)
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Duration</p>
                            <p class="font-semibold text-gray-900">{{ $course->duration_days }} days</p>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Instructor Information -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Instructor</h3>
                    <div class="flex items-center">
                        @php
                            $mentorPhotoPath = $course->mentor->photo
                                ? (Str::startsWith($course->mentor->photo, ['http://', 'https://', '/storage/']) 
                                    ? $course->mentor->photo 
                                    : Storage::url($course->mentor->photo))
                                : asset('assets/images/user-avatar.png');
                        @endphp
                        <img src="{{ $mentorPhotoPath }}" 
                             alt="{{ $course->mentor->user->name }}" 
                             class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900">{{ $course->mentor->user->name }}</h4>
                            <p class="text-sm text-gray-600">Course Instructor</p>
                            @if($course->mentor->bio)
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($course->mentor->bio, 150) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('courses.show', $course) }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-external-link-alt mr-2"></i>
                        Go to Course Page
                    </a>
                    
                    <a href="{{ $conversation ? route('chat.show', $conversation->unique_code) : route('chat.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-comment mr-2"></i>
                        Message Instructor
                    </a>
                </div>
            </div>
        </div>

        <!-- Enrollment Information Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Enrollment Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Enrollment Date -->
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fa-solid fa-calendar-plus text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Enrolled On</p>
                        <p class="font-semibold text-gray-900">{{ $enrollment->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                @php
                    $enrolledAt = $enrollment->enrolled_at ?? $enrollment->created_at;
                    $durationDays = $course->duration_days ?? 0;
                    $endDate = $enrolledAt ? \Carbon\Carbon::parse($enrolledAt)->addDays($durationDays) : null;
                    $now = \Carbon\Carbon::now();
                    $timeLeft = null;
                    if ($endDate && $now->lt($endDate)) {
                        $diff = $now->diff($endDate);
                        $days = $diff->d + ($diff->m * 30) + ($diff->y * 365); // handle months/years if any
                        $hours = $diff->h;
                        $minutes = $diff->i;
                        $timeLeftArr = [];
                        if ($days > 0) {
                            $timeLeftArr[] = $days . ' ' . Str::plural('day', $days);
                        }
                        if ($hours > 0) {
                            $timeLeftArr[] = $hours . ' ' . Str::plural('hour', $hours);
                        }
                        if ($minutes > 0 || empty($timeLeftArr)) {
                            $timeLeftArr[] = $minutes . ' ' . Str::plural('minute', $minutes);
                        }
                        $timeLeft = implode(', ', $timeLeftArr);
                    }
                @endphp
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fa-solid fa-hourglass-half text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Time Left</p>
                        <p class="font-semibold text-gray-900">
                            @if($timeLeft)
                                {{ $timeLeft }}
                            @else
                                0 minutes
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Payment Amount -->
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fa-solid fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Amount Paid</p>
                        <p class="font-semibold text-gray-900">
                            @if($enrollment->paymentTransaction)
                                ${{ number_format($enrollment->paymentTransaction->gross_amount, 2) }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <div class="w-10 h-10 {{ $enrollment->enrollment_status == 'active' ? 'bg-green-100' : 'bg-blue-100' }} rounded-lg flex items-center justify-center mr-3">
                        <i class="fa-solid {{ $enrollment->enrollment_status == 'active' ? 'fa-play-circle text-green-600' : 'fa-check-circle text-blue-600' }}"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-semibold text-gray-900 capitalize">{{ $enrollment->enrollment_status }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Content/Modules (if available) -->
        @if($course->subCategories->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Topics</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($course->subCategories as $subCategory)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-check text-purple-600 text-sm"></i>
                        </div>
                        <span class="font-medium text-gray-900">{{ $subCategory->name }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Reviews Section -->
        @if($course->reviews->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Reviews</h3>
            
            <!-- Average Rating -->
            <div class="flex items-center mb-6">
                @php
                    $averageRating = $course->reviews->avg('rating') ?? 0;
                    $totalReviews = $course->reviews->count();
                @endphp
                <div class="flex items-center mr-4">
                    <span class="text-3xl font-bold text-gray-900 mr-2">{{ number_format($averageRating, 1) }}</span>
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        @endfor
                    </div>
                </div>
                <span class="text-sm text-gray-500">({{ $totalReviews }} {{ Str::plural('review', $totalReviews) }})</span>
            </div>

            <!-- Recent Reviews -->
            <div class="space-y-4">
                @foreach($course->reviews->take(3) as $review)
                    <div class="border-b border-gray-200 pb-4 last:border-b-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fa-solid fa-user text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $review->user->name ?? 'Anonymous Student' }}</p>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} fill-current" viewBox="0 0 20 20">
                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-700">{{ $review->comment }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Review Form for Enrolled Users -->
        @php
            $existingReview = auth()->user()->reviews()->where('course_id', $course->id)->first();
        @endphp
        @include('components.review-form', [
            'type' => 'course',
            'item' => $course,
            'existingReview' => $existingReview
        ])
    </div>
@endsection
