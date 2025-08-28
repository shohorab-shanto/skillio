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
@endsection
