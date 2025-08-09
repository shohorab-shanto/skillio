<div class="bg-gray-50 py-16">
    <section class="px-6">
        <x-section-header title="What Makes Skillio Different?"
            subtitle="We believe our quality course can change a life and our thousands of learner already have." />
        
        <section class="max-w-[1400px] w-full mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pt-8">
            
            {{-- Card 1 --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 lg:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden relative">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-4xl lg:text-5xl font-bold text-blue-900 mb-2">12,000+</h2>
                    <p class="text-lg text-gray-700 font-medium">Top class courses</p>
                </div>
                
                <!-- Content Container -->
                <div class="relative min-h-[240px]">
                    <!-- Badges -->
                    <div class="absolute top-4 left-0 flex flex-col gap-3 z-10">
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            1 on 1 Mentorship
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            Online Courses
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            24/7 Support
                        </span>
                    </div>

                    <!-- Image -->
                    <div class="absolute -bottom-8 -right-4">
                        <img src="{{ asset('assets/images/book.png') }}" 
                             alt="Book Stack" 
                             class="w-52 h-64 lg:w-60 lg:h-72 object-contain opacity-90">
                    </div>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 lg:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden relative">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-4xl lg:text-5xl font-bold text-purple-900 mb-2">5,000+</h2>
                    <p class="text-lg text-gray-700 font-medium">Expert mentors</p>
                </div>
                
                <!-- Content Container -->
                <div class="relative min-h-[240px]">
                    <!-- Badges -->
                    <div class="absolute top-4 left-0 flex flex-col gap-3 z-10">
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            Verified Experts
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            Industry Leaders
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            Success Stories
                        </span>
                    </div>

                    <!-- Image -->
                    <div class="absolute -bottom-8 -right-4">
                        <img src="{{ asset('assets/images/boy.png') }}" 
                             alt="Expert Mentor" 
                             class="w-52 h-64 lg:w-60 lg:h-72 object-contain opacity-90">
                    </div>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 lg:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden relative md:col-span-2 lg:col-span-1">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-4xl lg:text-5xl font-bold text-green-900 mb-2">98%</h2>
                    <p class="text-lg text-gray-700 font-medium">Success rate</p>
                </div>
                
                <!-- Content Container -->
                <div class="relative min-h-[240px]">
                    <!-- Badges -->
                    <div class="absolute top-4 left-0 flex flex-col gap-3 z-10">
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            Career Growth
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            Skill Development
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            Job Placement
                        </span>
                    </div>

                    <!-- Image -->
                    <div class="absolute -bottom-8 -right-2">
                        <img src="{{ asset('assets/images/girl.png') }}" 
                             alt="Successful Learner" 
                             class="w-80 h-95 lg:w-80 lg:h-95 object-contain opacity-90">
                    </div>
                </div>
            </div>

        </section>
    </section>
</div>