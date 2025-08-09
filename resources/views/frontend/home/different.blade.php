<div class="bg-base-100">
<section class="mb-20">
    <x-section-header title="What Makes Skillio Different?"
        subtitle="We believe our quality course can change a life and our thousands of learner already have." />
    
    <section class="max-w-[1200px] w-full mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-8 gap-x-6 justify-items-center pt-5">
        {{-- card 1 --}}
        <div class="card bg-blue-50 rounded-xl p-7 w-full h-[361px]">
            <div>
                <h2 class="text-5xl font-semibold ml-10">12,000+</h2>
                <p class="text-base text-gray-700 ml-10">Top class courses</p>
            </div>
            <div class="relative">
                <!-- Badges on the left -->
                <div class="absolute top-10 left-1 flex flex-col gap-5 z-10 pl-10">
                    <p class="badge badge-md bg-white text-black shadow">1 on 1 Mentorship</p>
                    <p class="badge badge-md bg-white text-black shadow">Online Courses</p>
                    <p class="badge badge-md bg-white text-black shadow">24/7 Support</p>
                </div>

                <!-- Image shifted to the right -->
                <img src="{{ asset('assests/images/book.png') }}" alt="Book Stack" class="w-48 h-64 left-24 top-1 relative z-0">
            </div>
        </div>
       
        {{-- card 2 --}}
        <div class="card bg-blue-50 rounded-xl p-7 w-full h-[361px]">
            <div>
                <h2 class="text-5xl font-semibold ml-10">5,000+</h2>
                <p class="text-base text-gray-700 ml-10">Expert mentors</p>
            </div>
            <div class="relative">
                <!-- Badges on the left -->
                <div class="absolute top-10 left-1 flex flex-col gap-5 z-10 pl-10">
                    <p class="badge badge-md bg-white text-black shadow">Verified Experts</p>
                    <p class="badge badge-md bg-white text-black shadow">Industry Leaders</p>
                    <p class="badge badge-md bg-white text-black shadow">Success Stories</p>
                </div>

                <!-- Image shifted to the right -->
                <img src="{{ asset('assests/images/boy.png') }}" alt="Expert Mentor" class="w-48 h-64 left-24 top-1 relative z-0">
            </div>
        </div>

        {{-- card 3 --}}
        <div class="card bg-blue-50 rounded-xl p-7 w-full h-[361px]">
            <div>
                <h2 class="text-5xl font-semibold ml-10">98%</h2>
                <p class="text-base text-gray-700 ml-10">Success rate</p>
            </div>
            <div class="relative">
                <!-- Badges on the left -->
                <div class="absolute top-10 left-1 flex flex-col gap-5 z-10 pl-10">
                    <p class="badge badge-md bg-white text-black shadow">Career Growth</p>
                    <p class="badge badge-md bg-white text-black shadow">Skill Development</p>
                    <p class="badge badge-md bg-white text-black shadow">Job Placement</p>
                </div>

                <!-- Image shifted to the right -->
                <img src="{{ asset('assests/images/girl.png') }}" alt="Successful Learner" class="w-48 h-64 left-24 top-1 relative z-0">
            </div>
        </div>
    </section>
</section>
</div>