@extends('frontend.layouts.app')

@section('title', __('trans.mentors'))

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
            <div class="text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight">
                    {{ __('trans.find_perfect_mentor_page') }}
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    {{ __('trans.mentors_page_description') }}
                </p>
            </div>
            
                                <!-- Search Section -->
                    {{-- <div class="max-w-2xl mx-auto">
                        <form method="GET" action="{{ route('mentors') }}" class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('trans.search_for_mentors') }}"
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
                                    {{ __('trans.search_results_for_mentors') }} <span class="font-semibold text-purple-600">"{{ request('search') }}"</span>
                                </p>
                                <a href="{{ route('mentors') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                    {{ __('trans.clear_search_mentors') }}
                                </a>
                            </div>
                        @endif
                    </div> --}}
        </div>
    </section>

    <!-- Mentors Grid Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-[1400px] mx-auto px-6">
            <!-- Mentors Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($mentors as $mentor)
                    <div class="bg-white rounded-xl shadow-md p-6 w-full max-w-sm">
                        <div class="flex items-center space-x-4">
                            @if($mentor['photo'])
                                <img class="w-14 h-14 rounded-full object-cover" 
                                     src="{{ asset('storage/' . $mentor['photo']) }}" 
                                     alt="{{ $mentor['name'] }}" />
                            @else
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-lg">
                                    {{ strtoupper(substr($mentor['name'], 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">{{ $mentor['name'] }}</h2>
                                <p class="text-sm text-gray-500">
                                    {{ $mentor['work_experience'] ? Str::limit($mentor['work_experience'], 30) : __('trans.professional_mentor_alt') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center space-x-2">
                            <!-- Star Rating Display -->
                            <div class="flex items-center">
                                @php
                                    $rating = $mentor['average_rating'] ?? 0;
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
                                            <linearGradient id="half-star-{{ $mentor['id'] }}">
                                                <stop offset="50%" stop-color="#fbbf24"/>
                                                <stop offset="50%" stop-color="#e5e7eb"/>
                                            </linearGradient>
                                        </defs>
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" fill="url(#half-star-{{ $mentor['id'] }})"/>
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
                                                                    <span class="text-gray-500 text-xs">({{ $mentor['total_reviews'] ?? 0 }} {{ __('trans.reviews') }})</span>
                            </div>
                        </div>

                        <p class="mt-4 text-gray-700 text-sm">
                            {{ $mentor['bio'] ? Str::limit($mentor['bio'], 100) : __('trans.experienced_mentor_bio_alt') }}
                        </p>

                        <div class="flex flex-wrap gap-2 mt-4">
                            @if($mentor['top_category'])
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ $mentor['top_category'] }}</span>
                            @endif
                            @if($mentor['top_category_sub_categories'])
                                @foreach(array_slice($mentor['top_category_sub_categories'], 0, 3) as $subCategory)
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ $subCategory }}</span>
                                @endforeach
                            @endif
                            @if(!$mentor['top_category'] && !$mentor['top_category_sub_categories'])
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ __('trans.professional_alt') }}</span>
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">{{ __('trans.expert_alt') }}</span>
                            @endif
                        </div>

                        <div class="mt-5 flex justify-between items-center">
                            <div class="text-xl font-bold text-gray-900">
                               {{-- ${{ $mentor['lowest_session_rate'] ?? '0' }}<span class="text-sm font-normal text-gray-500">{{ __('trans.per_hour_alt') }}</span> --}}
                            </div>
                            <a href="{{ route('mentor.sessions', $mentor['id']) }}" 
                               class="bg-violet-600 hover:bg-violet-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">
                                {{ __('trans.book_session_alt') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <i class="fa-solid fa-user-tie text-4xl mb-4"></i>
                            <p class="text-lg font-medium">{{ __('trans.no_mentors_available_page') }}</p>
                            <p class="text-sm">{{ __('trans.check_back_later_page') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="flex justify-center items-center mt-12">
                <x-custom-pagination :paginator="$mentors->appends(request()->query())" />
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    @include('frontend.home.question-answer')
@endsection
