<div id="why-we-are-different" class="bg-gray-50">
    <section class="px-6">
        <x-section-header title="{{ __('trans.what_makes_skillio_different_title') }}"
            subtitle="{{ __('trans.what_makes_skillio_different_subtitle') }}" />
        
        <section class="max-w-[1400px] w-full mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pt-8">
            
            {{-- Card 1 --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 lg:p-8 shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden relative">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-4xl lg:text-5xl font-bold text-blue-900 mb-2">12,000+</h2>
                    <p class="text-lg text-gray-700 font-medium">{{ __('trans.top_class_courses') }}</p>
                </div>
                
                <!-- Content Container -->
                <div class="relative min-h-[240px]">
                    <!-- Badges -->
                    <div class="absolute top-4 left-0 flex flex-col gap-3 z-10">
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.one_on_one_mentorship') }}
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.online_courses_badge') }}
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.support_24_7') }}
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
                    <p class="text-lg text-gray-700 font-medium">{{ __('trans.expert_mentors_count') }}</p>
                </div>
                
                <!-- Content Container -->
                <div class="relative min-h-[240px]">
                    <!-- Badges -->
                    <div class="absolute top-4 left-0 flex flex-col gap-3 z-10">
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.verified_experts') }}
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.industry_leaders') }}
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.success_stories') }}
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
                    <p class="text-lg text-gray-700 font-medium">{{ __('trans.success_rate') }}</p>
                </div>
                
                <!-- Content Container -->
                <div class="relative min-h-[240px]">
                    <!-- Badges -->
                    <div class="absolute top-4 left-0 flex flex-col gap-3 z-10">
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.career_growth') }}
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.skill_development') }}
                        </span>
                        <span class="inline-block bg-white text-gray-800 px-3 py-1.5 rounded-full text-sm font-medium shadow-md">
                            {{ __('trans.job_placement') }}
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