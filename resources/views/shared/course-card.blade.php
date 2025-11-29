            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <a href="{{ route('courses.show', $course) }}" class="block relative p-2 group focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" 
                             class="w-full h-48 object-cover rounded-lg group-hover:opacity-90 transition-opacity duration-200">
                    @else
                        <div class="w-full h-48 flex items-center justify-center bg-purple-50 rounded-lg">
                            <svg class="w-16 h-16 text-purple-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute top-4 left-4">
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-3 py-1 rounded-full">{{ __('trans.online') }}</span>
                    </div>
                    @if($course->category)
                    <div class="absolute top-4 right-4">
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-3 py-1 rounded-full">{{ $course->category->name }}</span>
                    </div>
                    @endif
                </a>
                
                <div class="p-6">
                    <!-- Rating -->
                    @php
                        $averageRating = $course->averageRating() ?? 0;
                        $totalReviews = $course->totalReviews();
                        $fullStars = floor($averageRating);
                        $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
                    @endphp

                    <div class="flex items-center gap-2 mb-3">
                        <div class="flex text-orange-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $fullStars)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @elseif($i == $fullStars + 1 && $hasHalfStar)
                                    <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">{{ number_format($averageRating, 1) }} ({{ $totalReviews }} {{ __('trans.reviews_count') }})</span>
                    </div>
                    
                    <!-- Title -->
                    <h3 class="font-bold text-lg text-gray-900 mb-3">
                        <a href="{{ route('courses.show', $course) }}" class="hover:text-purple-600 transition-colors duration-200">
                            {{ $course->title }}
                        </a>
                    </h3>
                    
                    <!-- Duration & Date -->
                    <div class="flex justify-between text-sm text-gray-600 mb-4">
                        @if($course->duration_days)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $course->duration_days }} {{ __('trans.days') }}
                        </span>
                        @endif
                        @if($course->created_at)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $course->created_at->format('M d, Y') }}
                        </span>
                        @endif
                    </div>
                    
                    <!-- Instructor & Price -->
                    <div class="flex items-center justify-between">
                        @if($course->mentor)
                        <div class="flex items-center gap-2">
                            @php
                                $mentorPhotoPath = $course->mentor->photo
                                    ? (Str::startsWith($course->mentor->photo, ['http://', 'https://', '/storage/']) 
                                        ? $course->mentor->photo 
                                        : Storage::url($course->mentor->photo))
                                    : asset('assets/images/user-avatar.png');
                            @endphp
                            <img src="{{ $mentorPhotoPath }}" 
                                 alt="{{ $course->mentor->user->name }}" class="w-8 h-8 rounded-full object-cover"
                                 loading="lazy">
                            <span class="text-sm text-gray-700">{{ $course->mentor->user->name }}</span>
                        </div>
                        @endif
                        <div class="text-right">
                            @php
                                $currencySymbol = $course->currency == 'EUR' ? '€' : '$';
                            @endphp
                            @if($course->discount > 0)
                                <span class="text-sm text-gray-500 line-through">{{ $currencySymbol }}{{ number_format($course->price, 2) }}</span>
                                <span class="text-lg font-bold text-purple-600 ml-2">{{ $currencySymbol }}{{ number_format($course->discounted_price, 2) }}</span>
                            @else
                                <span class="text-lg font-bold text-purple-600">{{ $currencySymbol }}{{ number_format($course->price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>