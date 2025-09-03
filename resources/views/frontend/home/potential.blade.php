<section id="unleash-your-potential" class="bg-white">
    <div class="max-w-[1400px] mx-auto px-6">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <!-- Left Side - Centered Image -->
            <div class="lg:w-1/2 relative">
                <div class="relative w-full max-w-none sm:max-w-sm md:max-w-md lg:max-w-lg mx-auto">
                    <div class="mt-6 sm:mt-8 md:mt-10 bg-gradient-to-t from-purple-50 to-purple-100 rounded-3xl sm:rounded-4xl w-full">
                        <img 
                            src="{{ asset('assets/images/boy-bag.png') }}" 
                            alt="Professional with bag" 
                            class="w-full h-auto mx-auto max-w-full"
                        >
                    </div>
                    <img 
                        src="{{ asset('assets/images/group.png') }}" 
                        alt="Group illustration" 
                        class="absolute top-1 left-1 z-10 w-12 h-12 sm:w-20 sm:h-20 md:w-24 md:h-24"
                    >
                </div>
            </div>

            <!-- Right Side - Previous Content -->
            <div class="lg:w-1/2 flex-1 flex flex-col items-start justify-center pt-0 lg:pt-0 space-y-5 mb-20">
                <x-uplash-section-header 
                    title="{{ __('trans.unleash_potential_title') }}"
                    subtitle="{{ __('trans.unleash_potential_subtitle') }}"
                />
                
                <div class="flex flex-col items-start gap-3">
                    <img src="{{ asset('assets/images/statistic.png') }}" alt="Statistics icon" class="w-12 h-12">
                    <h3 class="font-semibold text-3xl">{{ __('trans.build_your_career') }}</h3>
                    <p class="text-gray-500 text-base">
                        {{ __('trans.build_career_description') }}
                    </p>
                </div>
                <hr class="w-full border-t border-gray-200 my-6">

                <div class="flex flex-col items-start gap-3">
                    <img src="{{ asset('assets/images/circle.png') }}" alt="Circle icon" class="w-12 h-12">
                    <h3 class="font-semibold text-3xl">{{ __('trans.develop_your_skills') }}</h3>
                    <p class="text-gray-500 text-base">
                        {{ __('trans.develop_skills_description') }}
                    </p>
                </div>
                <hr class="w-full border-t border-gray-200 my-6">
            </div>
        </div>
    </div>
</section>