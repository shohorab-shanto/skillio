<section id="testimonials">
<x-section-header title="What's Our Members Says"
    subtitle="Attached is just a small portion of the success of our members through our mentorships and courses." />

<section class="pb-16 mb-4 bg-gray-50">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Testimonials Container with Navigation -->
        <div class="relative">
            <!-- Navigation Arrows -->
            <button id="prev-reviews" class="absolute -left-12 top-1/2 transform -translate-y-1/2 z-10 w-12 h-12 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center transition-colors duration-200 shadow-lg">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <button id="next-reviews" class="absolute -right-12 top-1/2 transform -translate-y-1/2 z-10 w-12 h-12 bg-gray-800 hover:bg-gray-900 rounded-full flex items-center justify-center transition-colors duration-200 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Reviews Grid Container -->
            <div class="overflow-hidden">
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
                                                ({{ $topReviews->count() }} Reviews)
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
                                            <p class="text-sm text-gray-500">Digital Marketing Director</p>
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
                                    "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
                                </p>
                                
                                <!-- Reviewer Info -->
                                <div class="flex items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b47c?w=100" 
                                         alt="David Kim" 
                                         class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">David Kim</h4>
                                        <p class="text-sm text-gray-500">Digital Marketing Director</p>
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
                                    <span class="text-sm font-medium text-gray-700">4.8 (120 Reviews)</span>
                                </div>
                                
                                <!-- Quote Icon -->
                                <div class="mb-4">
                                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                
                                <!-- Review Text -->
                                <p class="text-gray-700 mb-6 leading-relaxed">
                                    "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
                                </p>
                                
                                <!-- Reviewer Info -->
                                <div class="flex items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100" 
                                         alt="David Kim" 
                                         class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">David Kim</h4>
                                        <p class="text-sm text-gray-500">Digital Marketing Director</p>
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
                                    "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
                                </p>
                                
                                <!-- Reviewer Info -->
                                <div class="flex items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100" 
                                         alt="David Kim" 
                                         class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">David Kim</h4>
                                        <p class="text-sm text-gray-500">Digital Marketing Director</p>
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
                                    <span class="text-sm font-medium text-gray-700">4.8 (120 Reviews)</span>
                                </div>
                                
                                <!-- Quote Icon -->
                                <div class="mb-4">
                                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                
                                <!-- Review Text -->
                                <p class="text-gray-700 mb-6 leading-relaxed">
                                    "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout."
                                </p>
                                
                                <!-- Reviewer Info -->
                                <div class="flex items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100" 
                                         alt="David Kim" 
                                         class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">David Kim</h4>
                                        <p class="text-sm text-gray-500">Digital Marketing Director</p>
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
    const prevBtn = document.getElementById('prev-reviews');
    const nextBtn = document.getElementById('next-reviews');
    const groups = container.querySelectorAll('[data-group]');
    
    let currentGroup = 0;
    const totalGroups = groups.length;
    
    // Hide navigation if only one group
    if (totalGroups <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
        return;
    }
    
    // Update navigation button states
    function updateNavigation() {
        prevBtn.disabled = currentGroup === 0;
        nextBtn.disabled = currentGroup === totalGroups - 1;
        
        prevBtn.style.opacity = currentGroup === 0 ? '0.5' : '1';
        nextBtn.style.opacity = currentGroup === totalGroups - 1 ? '0.5' : '1';
    }
    
    // Navigate to specific group
    function goToGroup(groupIndex) {
        currentGroup = groupIndex;
        const translateX = -groupIndex * 100;
        container.style.transform = `translateX(${translateX}%)`;
        updateNavigation();
    }
    
    // Previous button click
    prevBtn.addEventListener('click', function() {
        if (currentGroup > 0) {
            goToGroup(currentGroup - 1);
        }
    });
    
    // Next button click
    nextBtn.addEventListener('click', function() {
        if (currentGroup < totalGroups - 1) {
            goToGroup(currentGroup + 1);
        }
    });
    
    // Initialize navigation state
    updateNavigation();
});
</script>
