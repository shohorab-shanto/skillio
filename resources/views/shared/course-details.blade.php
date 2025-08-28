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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a2 2 0 00-9-5.197m13.5-9a2 2 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                        </svg>
                        <span>{{ $course->enrolledStudentsCount() }} {{ $course->enrolledStudentsCount() > 1 ? __('trans.students') : __('trans.student') }}</span>
                    </div>
                    @if($course->duration_days)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $course->duration_days }} {{ __('trans.days') }}</span>
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
                        <span class="text-gray-500 ml-1">({{ $totalReviews }} {{ __('trans.reviews') }})</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center">
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-8 flex-1">
                    @if($course->start_date)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-gray-900">{{ __('trans.start_date_label') }}</span>
                            <span>{{ $course->start_date->format('M d, Y') }}</span>
                        </div>
                    @endif
                    @if($course->end_date)
                        <div class="text-sm text-gray-600">
                            <span class="font-medium text-gray-900">{{ __('trans.end_date_label') }}</span>
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
                                {{ $course->discount }}% {{ __('trans.off') }}
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
                        ✓ {{ __('trans.already_enrolled') }}
                    </div>
                @else
                    <a href="{{ route('checkout.course', $course) }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg text-lg font-medium transition-colors inline-block">
                        {{ __('trans.enroll_now_price') }} ${{ number_format($course->discount > 0 ? $course->discounted_price : $course->price, 2) }}
                    </a>
                @endif
            </div>
            @endif

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('about')" id="about-tab"
                        class="tab-button text-purple-600 border-purple-600 py-4 px-1 border-b-2 text-sm font-medium">{{ __('trans.about') }}</button>
                    <button onclick="showTab('review')" id="review-tab"
                        class="tab-button text-gray-500 hover:text-gray-700 py-4 px-1 border-b-2 border-transparent text-sm font-medium">{{ __('trans.reviews_tab') }}</button>
                    @if($showEarningTab)
                    <button onclick="showTab('earning')" id="earning-tab"
                        class="tab-button text-gray-500 hover:text-gray-700 py-4 px-1 border-b-2 border-transparent text-sm font-medium">{{ __('trans.earning_history') }}</button>
                    @endif
                </nav>
            </div>

            <!-- Tab Content -->
            <div id="tab-content">
                
                <!-- About Tab -->
                <div id="about-content" class="tab-content">
                    <div class="max-w-none">
                        <h3 class="text-lg font-semibold mb-4">{{ __('trans.description') }}</h3>
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $course->description }}</div>
                        
                        @if($course->subCategories->count() > 0)
                        <h3 class="text-lg font-semibold mt-6 mb-4">{{ __('trans.what_youll_learn') }}</h3>
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
                    <h3 class="text-lg font-semibold mb-6">{{ __('trans.student_reviews') }}</h3>
                    
                    @if($course->reviews->count() > 0)
                        <!-- Review Statistics -->
                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                            <h4 class="text-md font-semibold mb-4">{{ __('trans.review_statistics') }}</h4>
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
                                    <p class="text-gray-600">{{ __('trans.based_on_reviews') }} {{ $course->reviews->count() }} {{ __('trans.reviews_count') }}</p>
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
                        <h4 class="text-md font-semibold mb-4">{{ __('trans.recent_reviews') }}</h4>
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
                                                <p class="font-medium text-gray-900">{{ $review->user->name ?? __('trans.anonymous_student') }}</p>
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
                            <p class="text-gray-500">{{ __('trans.no_reviews_yet') }}</p>
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
                        {{-- card 1: Currently Enrolled --}}
                        <div class="card bg-white w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838l-2.727 1.17 1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.882l1.818.78a3 3 0 002.482 2.88z"/>
                                    </svg>
                                </div>
                                <span class="text-xl text-gray-500">{{ __('trans.currently_enrolled') }}</span>
                            </div>
                            <p class="text-2xl font-extrabold mt-3 text-black">{{ $course->currentlyEnrolledCount() }}</p>
                        </div>
                        {{-- card 2: Total Enrolled --}}
                        <div class="card bg-white w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                                    </svg>
                                </div>
                                <span class="text-xl text-gray-500">{{ __('trans.total_enrolled_students') }}</span>
                            </div>
                            <p class="text-2xl font-extrabold mt-3 text-black">{{ $course->enrolledStudentsCount() }}</p>
                        </div>
                        {{-- card 3: Total Income --}}
                        <div class="card bg-white w-full h-36 rounded-xl max-w-sm flex flex-col justify-center pl-10 shadow-sm border border-gray-100">
                            <div class="flex gap-2 items-center">
                                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-xl text-gray-500">{{ __('trans.total_income') }}</span>
                            </div>
                            <p class="text-2xl font-extrabold mt-3 text-black">${{ number_format($course->totalIncome(), 2) }}</p>
                        </div>
                    </section>

                    <!-- Enrolled Students List -->
                    <div class="mt-8">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-xl font-bold text-gray-900">{{ __('trans.student_list') }}</h3>
                                    <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full">
                                        ({{ $course->enrolledStudentsCount() }})
                                    </span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" id="studentSearch" placeholder="{{ __('trans.search') }}" 
                                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm w-64">
                                    </div>
                                    <div class="relative">
                                        <button id="filterBtn" class="flex items-center space-x-2 bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <span>{{ __('trans.filter') }}</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div id="filterDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10 hidden">
                                            <div class="py-1">
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-filter="all">{{ __('trans.all') }}</button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-filter="active">{{ __('trans.active') }}</button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 bg-purple-100 text-purple-700" data-filter="inactive">{{ __('trans.inactive') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Students Table -->
                            <div class="overflow-x-auto">
                                <div class="min-w-full">
                                    <!-- Table Headers -->
                                    <div class="grid grid-cols-5 gap-4 pb-3 border-b border-gray-200 mb-4">
                                        <div class="text-sm font-medium text-gray-700">{{ __('trans.student_name') }}</div>
                                        <div class="text-sm font-medium text-gray-700">{{ __('trans.date') }}</div>
                                        <div class="text-sm font-medium text-gray-700">{{ __('trans.duration_left_days') }}</div>
                                        <div class="text-sm font-medium text-gray-700">{{ __('trans.status') }}</div>
                                        <div class="text-sm font-medium text-gray-700"></div>
                                    </div>

                                    <!-- Students List -->
                                    <div id="studentsList" class="space-y-3">
                                        @php
                                            $enrolledStudents = $course->enrolledStudents();
                                        @endphp
                                        
                                        @forelse($enrolledStudents as $enrollment)
                                            <div class="grid grid-cols-5 gap-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors student-row" 
                                                 data-status="{{ $enrollment->enrollment_status }}">
                                                <!-- Student Name -->
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ __('trans.student_id') }}-{{ $enrollment->user->id }}</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Date -->
                                                <div class="text-sm text-gray-600">
                                                    {{ $enrollment->created_at->format('F d, Y') }}
                                                </div>
                                                
                                                <!-- Duration Left -->
                                                <div class="flex items-center">
                                                    <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full">
                                                        {{ $course->end_date ? max(0, $course->end_date->diffInDays(now())) : __('trans.na') }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Status -->
                                                <div class="flex items-center">
                                                    @if($enrollment->enrollment_status === 'active')
                                                        <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">{{ __('trans.active') }}</span>
                                                    @else
                                                                                                                  <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">{{ __('trans.inactive') }}</span>
                                                    @endif
                                                </div>
                                                
                                                <!-- Chat Button -->
                                                <div class="flex items-center">
                                                    @php
                                                        $conversation = \App\Models\Conversation::where('mentor_id', $course->mentor_id)
                                                            ->where('user_id', $enrollment->user_id)
                                                            ->first();
                                                    @endphp
                                                    @if($conversation)
                                                        <a href="{{ route('chat.show', $conversation->unique_code) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                            </svg>
                                                            <span>{{ __('trans.chat') }}</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('chat.index', ['user_id' => $enrollment->user_id, 'mentor_id' => $course->mentor_id]) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            <span>{{ __('trans.start_chat') }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-8">
                                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <p class="text-gray-500">{{ __('trans.no_students_enrolled_yet') }}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            @if($enrolledStudents->hasPages())
                                <div class="mt-6 flex items-center justify-center">
                                    <div class="flex items-center space-x-2">
                                        @if($enrolledStudents->onFirstPage())
                                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-default">{{ __('trans.previous') }}</span>
                                        @else
                                            <a href="{{ $enrolledStudents->appends(request()->query())->previousPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ __('trans.previous') }}</a>
                                        @endif
                                        
                                        @foreach($enrolledStudents->getUrlRange(1, $enrolledStudents->lastPage()) as $page => $url)
                                            <a href="{{ $url }}" class="px-3 py-2 text-sm font-medium {{ $page == $enrolledStudents->currentPage() ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 rounded-lg">
                                                {{ $page }}
                                            </a>
                                        @endforeach
                                        
                                        @if($enrolledStudents->hasMorePages())
                                            <a href="{{ $enrolledStudents->appends(request()->query())->nextPageUrl() }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ __('trans.next') }}</a>
                                        @else
                                            <span class="px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-default">{{ __('trans.next') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
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
    
    // Initialize student list functionality
    initializeStudentList();
});

function initializeStudentList() {
    const filterBtn = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const studentSearch = document.getElementById('studentSearch');
    
    if (filterBtn && filterDropdown) {
        // Toggle filter dropdown
        filterBtn.addEventListener('click', function() {
            filterDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!filterBtn.contains(event.target) && !filterDropdown.contains(event.target)) {
                filterDropdown.classList.add('hidden');
            }
        });
        
        // Handle filter selection
        filterDropdown.addEventListener('click', function(event) {
            if (event.target.dataset.filter) {
                const filter = event.target.dataset.filter;
                
                // Update active filter button
                filterDropdown.querySelectorAll('button').forEach(btn => {
                    btn.classList.remove('bg-purple-100', 'text-purple-700');
                    btn.classList.add('text-gray-700', 'hover:bg-gray-100');
                });
                event.target.classList.remove('text-gray-700', 'hover:bg-gray-100');
                event.target.classList.add('bg-purple-100', 'text-purple-700');
                
                // Update filter button text
                filterBtn.querySelector('span').textContent = event.target.textContent;
                
                // Hide dropdown
                filterDropdown.classList.add('hidden');
                
                // Apply filter to student rows
                const studentRows = document.querySelectorAll('.student-row');
                studentRows.forEach(row => {
                    const status = row.dataset.status;
                    if (filter === 'all' || status === filter) {
                        row.style.display = 'grid';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    }
    
    if (studentSearch) {
        // Handle search functionality
        studentSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const studentRows = document.querySelectorAll('.student-row');
            const currentFilter = document.querySelector('#filterDropdown button[class*="bg-purple-100"]')?.dataset.filter || 'all';
            
            studentRows.forEach(row => {
                const studentName = row.querySelector('.font-medium').textContent.toLowerCase();
                const studentId = row.querySelector('.text-sm.text-gray-500').textContent.toLowerCase();
                const status = row.dataset.status;
                
                const matchesSearch = studentName.includes(searchTerm) || studentId.includes(searchTerm);
                const matchesFilter = currentFilter === 'all' || status === currentFilter;
                
                if (matchesSearch && matchesFilter) {
                    row.style.display = 'grid';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}
</script>
