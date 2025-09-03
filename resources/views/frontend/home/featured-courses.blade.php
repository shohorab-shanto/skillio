<!-- Featured Courses Section -->
<section class="py-16 bg-gradient-to-r from-purple-50 to-indigo-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                {{ __('trans.explore_featured_courses') }}
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                {{ __('trans.discover_handpicked_featured_courses') }}
            </p>
        </div>

        @if($featuredCourses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($featuredCourses as $course)
                    @include('shared.course-card', ['course' => $course])
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('courses') }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-star mr-2"></i>
                    {{ __('trans.view_all_courses') }}
                </a>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-6 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-star text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('trans.no_featured_courses_yet') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('trans.working_on_featuring_best_courses') }}</p>
                <a href="{{ route('courses') }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fa-solid fa-book mr-2"></i>
                    {{ __('trans.browse_all_courses') }}
                </a>
            </div>
        @endif
    </div>
</section>
