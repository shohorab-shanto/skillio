<section id="new-courses" class="bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <x-section-header 
            title="Explore New Courses"
            subtitle="Discover the latest courses from expert mentors, fresh content tailored to your learning preferences."
            class="mb-8"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        @forelse($newCourses as $course)
            @include('shared.course-card', ['course' => $course])
        @empty
            <div class="col-span-full text-center py-12">
                <div class="text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.746 0-3.332-.523-4.5-1.253"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No new courses available at the moment</h3>
                    <p class="text-gray-600">We're working on adding more courses to our platform.</p>
                </div>
            </div>
        @endforelse
        </div>
        
        <!-- View All Courses Button -->
        <div class="flex justify-center mt-12">
            <a href="{{ route('courses') }}" 
               class="inline-flex items-center px-6 py-3 border-2 border-violet-600 text-violet-600 rounded-lg font-semibold hover:bg-violet-600 hover:text-white transition-all duration-300">
                View All Courses
                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>
