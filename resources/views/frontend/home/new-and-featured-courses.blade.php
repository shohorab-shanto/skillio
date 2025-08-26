<!-- New and Featured Courses Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Section Header with Tabs -->
        <div class="text-center mb-12">
            <div class="flex justify-center mb-6">
                <div class="bg-white rounded-lg p-1 shadow-sm border">
                    <button id="new-courses-tab" 
                            class="tab-button px-6 py-3 rounded-md font-semibold transition-all duration-200 active"
                            data-tab="new-courses">
                        New Courses
                    </button>
                    <button id="featured-courses-tab" 
                            class="tab-button px-6 py-3 rounded-md font-semibold transition-all duration-200"
                            data-tab="featured-courses">
                        Featured Courses
                    </button>
                </div>
            </div>
            
            <h2 id="section-title" class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Explore New Courses
            </h2>
            <p id="section-description" class="text-lg text-gray-600 max-w-2xl mx-auto">
                Here are the most popular courses and mentorships available on Skillio.
            </p>
        </div>

        <!-- New Courses Tab Content -->
        <div id="new-courses-content" class="tab-content active">
            @if($newCourses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($newCourses as $course)
                        @include('shared.course-card', ['course' => $course])
                    @endforeach
                </div>

                <div class="flex justify-center mt-12">
                    <a href="{{ route('courses') }}" 
                       class="inline-flex items-center px-6 py-3 border-2 border-violet-600 text-violet-600 rounded-lg font-semibold hover:bg-violet-600 hover:text-white transition-all duration-300">
                        View All Courses
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></svg>
                        </svg>
                    </a>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-book text-3xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No New Courses Yet</h3>
                    <p class="text-gray-600 mb-6">We're working on adding new courses for you. Check back soon!</p>
                    <a href="{{ route('courses') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fa-solid fa-book mr-2"></i>
                        Browse All Courses
                    </a>
                </div>
            @endif
        </div>

        <!-- Featured Courses Tab Content -->
        <div id="featured-courses-content" class="tab-content">
            @if($featuredCourses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredCourses as $course)
                        @include('shared.course-card', ['course' => $course])
                    @endforeach
                </div>

                <div class="flex justify-center mt-12">
                    <a href="{{ route('courses') }}" 
                       class="inline-flex items-center px-6 py-3 border-2 border-violet-600 text-violet-600 rounded-lg font-semibold hover:bg-violet-600 hover:text-white transition-all duration-300">
                        View All Courses
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></svg>
                        </svg>
                    </a>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-star text-3xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Featured Courses Yet</h3>
                    <p class="text-gray-600 mb-6">We're working on featuring the best courses for you. Check back soon!</p>
                    <a href="{{ route('courses') }}" 
                       class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <i class="fa-solid fa-book mr-2"></i>
                        Browse All Courses
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>

<style>
.tab-button {
    color: #6b7280;
    background: transparent;
}

.tab-button.active {
    color: white;
    background: #7c3aed;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    const sectionTitle = document.getElementById('section-title');
    const sectionDescription = document.getElementById('section-description');

    // Tab content data
    const tabData = {
        'new-courses': {
            title: 'Explore New Courses',
            description: 'Here are the most popular courses and mentorships available on Skillio.'
        },
        'featured-courses': {
            title: 'Explore Featured Courses',
            description: 'Discover our handpicked featured courses that stand out for their quality, content, and student satisfaction.'
        }
    };

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab + '-content').classList.add('active');
            
            // Update section title and description
            sectionTitle.textContent = tabData[targetTab].title;
            sectionDescription.textContent = tabData[targetTab].description;
        });
    });
});
</script>
