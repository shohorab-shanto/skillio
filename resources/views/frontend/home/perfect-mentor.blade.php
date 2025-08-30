<section id="mentors" class="py-16 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <x-section-header 
            title="{{ __('trans.find_perfect_mentor_title') }}"
            subtitle="{{ __('trans.find_perfect_mentor_subtitle') }}"
            class="mb-8"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">
        
        @forelse($topMentors as $mentor)
        <div class="bg-white rounded-xl shadow-md p-6 w-full max-w-sm flex flex-col h-full">
            <div class="flex items-center space-x-4">
                @if($mentor->photo)
                    <img class="w-14 h-14 rounded-full object-cover" 
                         src="{{ asset('storage/' . $mentor->photo) }}" 
                         alt="{{ $mentor->user->name }}" />
                @else
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($mentor->user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $mentor->user->name }}</h2>
                    <p class="text-sm text-gray-500">
                        {{ $mentor->work_experience ? Str::limit($mentor->work_experience, 30) : __('trans.professional_mentor') }}
                    </p>
                </div>
            </div>

            <div class="mt-3 flex items-center space-x-2">
                <!-- Star Rating Display -->
                <div class="flex items-center">
                    @php
                        $rating = $mentor->reviews_avg_rating ?? 0;
                        $fullStars = floor($rating);
                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                    @endphp
                    
                    <!-- Full Stars -->
                    @for($i = 0; $i < $fullStars; $i++)
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                    @endfor
                    
                    <!-- Half Star -->
                    @if($hasHalfStar)
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <defs>
                                <linearGradient id="half-star-{{ $mentor->id }}">
                                    <stop offset="50%" stop-color="#fbbf24"/>
                                    <stop offset="50%" stop-color="#e5e7eb"/>
                                </linearGradient>
                            </defs>
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" fill="url(#half-star-{{ $mentor->id }})"/>
                        </svg>
                    @endif
                    
                    <!-- Empty Stars -->
                    @for($i = 0; $i < $emptyStars; $i++)
                        <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                    @endfor
                </div>
                
                <!-- Rating Number and Reviews Count -->
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-gray-900 text-sm">{{ number_format($rating, 1) }}</span>
                    <span class="text-gray-500 text-xs">({{ $mentor->total_reviews ?? 0 }} {{ __('trans.reviews') }})</span>
                </div>
            </div>

            <p class="mt-4 text-gray-700 text-sm">
                {{ $mentor->bio ? Str::limit($mentor->bio, 100) : __('trans.experienced_mentor_bio') }}
            </p>

            <div class="flex flex-wrap gap-2 mt-4">
                @if($mentor->sessionBookings->isNotEmpty())
                    @php
                        $categories = $mentor->sessionBookings->pluck('category.name')->unique()->take(3);
                        $subCategories = $mentor->sessionBookings->pluck('subCategories.*.name')->flatten()->unique()->take(2);
                        $tags = $categories->merge($subCategories)->take(4);
                    @endphp
                    
                    @foreach($tags as $tag)
                        @if($tag)
                            <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ $tag }}</span>
                        @endif
                    @endforeach
                @else
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ __('trans.professional') }}</span>
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ __('trans.expert') }}</span>
                @endif
            </div>

            <!-- Spacer to push button to bottom -->
            <div class="flex-1"></div>

            <div class="mt-auto pt-4 flex justify-between items-center">
                <div class="text-xl font-bold text-gray-900">
                    ${{ $mentor->lowest_session_rate ?? 'N/A' }}<span class="text-sm font-normal text-gray-500">{{ __('trans.per_hour') }}</span>
                </div>
                <a href="{{ route('mentor.sessions', $mentor->id) }}" 
                   class="bg-violet-600 hover:bg-violet-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                    {{ __('trans.book_session') }}
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="text-gray-500">
                <i class="fa-solid fa-user-tie text-4xl mb-4"></i>
                <p class="text-lg font-medium">{{ __('trans.no_mentors_available') }}</p>
                <p class="text-sm">{{ __('trans.check_back_later') }}</p>
            </div>
        </div>
        @endforelse
        </div>
        
        <!-- View All Mentors Button -->
        <div class="flex justify-center mt-12">
            <a href="{{ route('mentors') }}" 
               class="inline-flex items-center px-6 py-3 border-2 border-violet-600 text-violet-600 rounded-lg font-semibold hover:bg-violet-600 hover:text-white transition-all duration-300">
                {{ __('trans.view_all_mentors') }}
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
