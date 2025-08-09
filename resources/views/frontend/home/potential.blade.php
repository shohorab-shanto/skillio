<section class="bg-white">
    <div class="max-w-[1400px] mx-auto px-6">
        <div class="flex flex-col lg:flex-row items-center gap-12">
            <!-- Left Side - Centered Image -->
            <div class="lg:w-1/2 relative">
                <div class="relative w-fit mx-auto max-w-lg"> <!-- Changed mr-auto to mx-auto for center alignment -->
                    <div class="mt-10 bg-gradient-to-t from-purple-50 to-purple-100 rounded-4xl">
                        <img src="{{ asset('assets/images/boy-bag.png') }}" alt="Professional with bag" class="w-full h-auto">
                    </div>
                    <img src="{{ asset('assets/images/group.png') }}" alt="Group illustration" class="absolute top-2 left-2 z-10 w-24 h-24">
                </div>
            </div>

            <!-- Right Side - Previous Content -->
            <div class="lg:w-1/2 flex-1 flex flex-col items-start justify-center pt-12 lg:pt-0 space-y-8">
                <x-section-header 
                    title="Unleash Your Potential with our educational platform"
                    subtitle="Start earning money online with quality mentoring on the Skillio platform."
                />
                
                <div class="flex flex-col items-start gap-3">
                    <img src="{{ asset('assets/images/statistic.png') }}" alt="Statistics icon" class="w-12 h-12">
                    <h3 class="font-semibold text-3xl">Build Your Career</h3>
                    <p class="text-gray-500 text-base">
                        With quality mentoring, your business career will be twice as fast.
                    </p>
                </div>

                <div class="flex flex-col items-start gap-3">
                    <img src="{{ asset('assets/images/circle.png') }}" alt="Circle icon" class="w-12 h-12">
                    <h3 class="font-semibold text-3xl">Develop Your Skills</h3>
                    <p class="text-gray-500 text-base">
                        On the platform you have access to all the modern skills for making money online.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>