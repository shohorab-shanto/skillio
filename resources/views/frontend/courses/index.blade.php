@extends('frontend.layouts.app')

@section('title', 'Courses')

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
    <!-- Hero Section -->
    <section class="bg-gray-50 pt-32 pb-5">
        <div class="max-w-[1400px] mx-auto px-6">
            
                    <!-- Search Section -->
                    <div class="max-w-2xl mx-auto">
                        <form method="GET" action="{{ route('courses') }}" class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('trans.search_for_courses') }}"
                                class="w-full pl-12 pr-4 py-4 text-lg border border-gray-300 rounded-2xl focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all duration-300 shadow-sm hover:shadow-md"
                            />
                            <button type="submit" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <svg class="h-5 w-5 text-gray-400 hover:text-purple-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                        
                        @if(request('search'))
                            <div class="mt-4 text-center">
                                <p class="text-gray-600 mb-2">
                                    {{ __('trans.search_results_for') }} <span class="font-semibold text-purple-600">"{{ request('search') }}"</span>
                                </p>
                                <a href="{{ route('courses') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                    {{ __('trans.clear_search') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Category Filter -->
                    <div class="mt-8">
                        <div class="max-w-[1400px] mx-auto px-6">
                            <div class="flex flex-wrap gap-3 justify-center">
                                <!-- All Categories Button -->
                                <a href="{{ route('courses', ['search' => request('search')]) }}" 
                                   class="px-6 py-2 rounded-full text-sm font-medium transition-colors duration-200 shadow-sm {{ !request('category') ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                    {{ __('trans.all') }}
                                </a>
                                
                                <!-- Individual Category Buttons -->
                                @foreach($categories as $category)
                                    <a href="{{ route('courses', ['category' => $category->id, 'search' => request('search')]) }}" 
                                       class="px-6 py-2 rounded-full text-sm font-medium transition-colors duration-200 shadow-sm {{ request('category') == $category->id ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
        </div>
    </section>


    <!-- Mentors Grid Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-[1400px] mx-auto px-6">
            <!-- Mentors Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($courses as $course)
                    @include('shared.course-card', ['course' => $course])
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('trans.no_courses_found') }}</h3>
                            <p class="text-gray-600">{{ __('trans.working_on_adding_courses') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($courses->hasPages())
            <div class="flex justify-center items-center mt-12">
                <x-custom-pagination :paginator="$courses->appends(request()->query())" />
            </div>
            @endif
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials">
        <x-section-header title="{{ __('trans.what_our_members_say_title') }}"
            subtitle="{{ __('trans.what_our_members_say_subtitle') }}" />

        <section class="pb-16 mb-4 bg-gray-50">
            <div class="max-w-[1400px] mx-auto px-6">
                <!-- Testimonials Container with Drag Support -->
                <div class="relative">
                    <!-- Reviews Grid Container -->
                    <div class="overflow-hidden cursor-grab active:cursor-grabbing" id="reviews-wrapper">
                        <div id="reviews-container" class="flex transition-transform duration-300 ease-in-out">
                            @forelse($topReviews->chunk(4) as $index => $reviewGroup)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full flex-shrink-0" data-group="{{ $index }}">
                                    @foreach($reviewGroup as $review)
                                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100">
                                            <!-- Rating -->
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="flex text-orange-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                            </svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ number_format($review->rating, 1) }}
                                                    @if($loop->index >= 2)
                                                        ({{ $topReviews->count() }} {{ __('trans.reviews_count_with_number') }})
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <!-- Review Text -->
                                            <p class="text-gray-700 mb-6 leading-relaxed">
                                                "{{ Str::limit($review->comment, 150) }}"
                                            </p>
                                            
                                            <!-- Reviewer Info -->
                                            <div class="flex items-center gap-3">
                                                @if($review->user && $review->user->profile_photo)
                                                    <img src="{{ asset('storage/' . $review->user->profile_photo) }}" 
                                                         alt="{{ $review->user->name }}" 
                                                         class="w-12 h-12 rounded-full object-cover">
                                                @else
                                                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                                        <span class="text-lg font-semibold text-purple-600">
                                                            {{ substr($review->user->name ?? 'U', 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h4 class="font-semibold text-gray-900">{{ $review->user->name ?? 'David Kim' }}</h4>
                                                    <p class="text-sm text-gray-500">{{ __('trans.digital_marketing_director') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <!-- Fallback to static testimonials if no reviews -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full flex-shrink-0">
                                    <!-- Testimonial Card 1 -->
                                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100">
                                        <!-- Rating -->
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="flex text-orange-400">
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">4.8</span>
                                        </div>
                                        
                                        <!-- Quote Icon -->
                                        <div class="mb-4">
                                            <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        
                                        <!-- Review Text -->
                                        <p class="text-gray-700 mb-6 leading-relaxed">
                                            "{{ __('trans.fallback_testimonial_text') }}"
                                        </p>
                                        
                                        <!-- Reviewer Info -->
                                        <div class="flex items-center gap-3">
                                            <img src="https://images.unsplash.com/photo-1494790108755-2616b612b47c?w=100" 
                                                 alt="David Kim" 
                                                 class="w-12 h-12 rounded-full object-cover">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">David Kim</h4>
                                                <p class="text-sm text-gray-500">{{ __('trans.digital_marketing_director') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Testimonial Card 2 -->
                                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100">
                                        <!-- Rating -->
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="flex text-orange-400">
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">4.8 (120 {{ __('trans.reviews_count_with_number') }})</span>
                                        </div>
                                        
                                        <!-- Quote Icon -->
                                        <div class="mb-4">
                                            <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        
                                        <!-- Review Text -->
                                        <p class="text-gray-700 mb-6 leading-relaxed">
                                            "{{ __('trans.fallback_testimonial_text') }}"
                                        </p>
                                        
                                        <!-- Reviewer Info -->
                                        <div class="flex items-center gap-3">
                                            <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100" 
                                                 alt="David Kim" 
                                                 class="w-12 h-12 rounded-full object-cover">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">David Kim</h4>
                                                <p class="text-sm text-gray-500">{{ __('trans.digital_marketing_director') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Testimonial Card 3 -->
                                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100">
                                        <!-- Rating -->
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="flex text-orange-400">
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">4.8</span>
                                        </div>
                                        
                                        <!-- Quote Icon -->
                                        <div class="mb-4">
                                            <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        
                                        <!-- Review Text -->
                                        <p class="text-gray-700 mb-6 leading-relaxed">
                                            "{{ __('trans.fallback_testimonial_text') }}"
                                        </p>
                                        
                                        <!-- Reviewer Info -->
                                        <div class="flex items-center gap-3">
                                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100" 
                                                 alt="David Kim" 
                                                 class="w-12 h-12 rounded-full object-cover">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">David Kim</h4>
                                                <p class="text-sm text-gray-500">{{ __('trans.digital_marketing_director') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Testimonial Card 4 -->
                                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-6 border border-gray-100">
                                        <!-- Rating -->
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="flex text-orange-400">
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-12 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                                <svg class="w-5 h-5 fill-current text-gray-300" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">4.8 (120 {{ __('trans.reviews_count_with_number') }})</span>
                                        </div>
                                        
                                        <!-- Quote Icon -->
                                        <div class="mb-4">
                                            <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                        
                                        <!-- Review Text -->
                                        <p class="text-gray-700 mb-6 leading-relaxed">
                                            "{{ __('trans.fallback_testimonial_text') }}"
                                        </p>
                                        
                                        <!-- Reviewer Info -->
                                        <div class="flex items-center gap-3">
                                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100" 
                                                 alt="David Kim" 
                                                 class="w-12 h-12 rounded-full object-cover">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">David Kim</h4>
                                                <p class="text-sm text-gray-500">{{ __('trans.digital_marketing_director') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('reviews-container');
        const wrapper = document.getElementById('reviews-wrapper');
        const groups = container.querySelectorAll('[data-group]');
        
        let currentGroup = 0;
        const totalGroups = groups.length;
        let isDragging = false;
        let startX = 0;
        let currentX = 0;
        let initialTransform = 0;
        let autoScrollInterval = null;
        const autoScrollDelay = 5000; // 5 seconds
        
        // Return early if only one group
        if (totalGroups <= 1) {
            return;
        }
        
        // Navigate to specific group
        function goToGroup(groupIndex) {
            currentGroup = Math.max(0, Math.min(groupIndex, totalGroups - 1));
            const translateX = -currentGroup * 100;
            container.style.transform = `translateX(${translateX}%)`;
        }
        
        // Auto scroll to next group
        function autoScrollNext() {
            if (isDragging) return;
            
            if (currentGroup < totalGroups - 1) {
                goToGroup(currentGroup + 1);
            } else {
                // Loop back to first group
                goToGroup(0);
            }
        }
        
        // Start auto scroll
        function startAutoScroll() {
            if (autoScrollInterval) return;
            autoScrollInterval = setInterval(autoScrollNext, autoScrollDelay);
        }
        
        // Stop auto scroll
        function stopAutoScroll() {
            if (autoScrollInterval) {
                clearInterval(autoScrollInterval);
                autoScrollInterval = null;
            }
        }
        
        // Handle drag start
        function handleDragStart(e) {
            isDragging = true;
            stopAutoScroll(); // Stop auto scroll when user starts dragging
            startX = e.type === 'mousedown' ? e.clientX : e.touches[0].clientX;
            currentX = startX;
            initialTransform = -currentGroup * 100;
            container.style.transition = 'none';
            wrapper.style.cursor = 'grabbing';
            
            // Prevent text selection during drag
            e.preventDefault();
        }
        
        // Handle drag move
        function handleDragMove(e) {
            if (!isDragging) return;
            
            currentX = e.type === 'mousemove' ? e.clientX : e.touches[0].clientX;
            const deltaX = currentX - startX;
            const sensitivity = 0.3; // Adjust sensitivity (lower = more sensitive)
            const translateX = initialTransform + (deltaX * sensitivity);
            
            // Apply transform with boundaries
            const maxTranslate = 0;
            const minTranslate = -(totalGroups - 1) * 100;
            const constrainedTranslate = Math.max(minTranslate, Math.min(maxTranslate, translateX));
            
            container.style.transform = `translateX(${constrainedTranslate}%)`;
        }
        
        // Handle drag end
        function handleDragEnd(e) {
            if (!isDragging) return;
            
            isDragging = false;
            container.style.transition = 'transform 0.3s ease-in-out';
            wrapper.style.cursor = 'grab';
            
            const deltaX = currentX - startX;
            const threshold = 50; // Minimum drag distance to trigger navigation
            
            if (Math.abs(deltaX) > threshold) {
                if (deltaX > 0 && currentGroup > 0) {
                    // Dragged right - go to previous group
                    goToGroup(currentGroup - 1);
                } else if (deltaX < 0 && currentGroup < totalGroups - 1) {
                    // Dragged left - go to next group
                    goToGroup(currentGroup + 1);
                } else {
                    // Snap back to current group
                    goToGroup(currentGroup);
                }
            } else {
                // Snap back to current group
                goToGroup(currentGroup);
            }
            
            // Restart auto scroll after a short delay
            setTimeout(() => {
                startAutoScroll();
            }, 1000);
        }
        
        // Mouse events
        wrapper.addEventListener('mousedown', handleDragStart);
        document.addEventListener('mousemove', handleDragMove);
        document.addEventListener('mouseup', handleDragEnd);
        
        // Touch events for mobile
        wrapper.addEventListener('touchstart', function(e) {
            e.preventDefault();
            handleDragStart(e);
            stopAutoScroll();
        }, { passive: false });
        document.addEventListener('touchmove', handleDragMove, { passive: false });
        document.addEventListener('touchend', handleDragEnd);
        
        // Touch events to pause auto scroll on mobile
        wrapper.addEventListener('touchend', function() {
            setTimeout(() => {
                startAutoScroll();
            }, 2000); // Longer delay on mobile
        });
        
        // Keyboard navigation (optional)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && currentGroup > 0) {
                stopAutoScroll();
                goToGroup(currentGroup - 1);
                setTimeout(() => {
                    startAutoScroll();
                }, 1000);
            } else if (e.key === 'ArrowRight' && currentGroup < totalGroups - 1) {
                stopAutoScroll();
                goToGroup(currentGroup + 1);
                setTimeout(() => {
                    startAutoScroll();
                }, 1000);
            }
        });
        
        // Initialize
        goToGroup(0);
        startAutoScroll(); // Start auto scrolling
    });
    </script>
@endsection
