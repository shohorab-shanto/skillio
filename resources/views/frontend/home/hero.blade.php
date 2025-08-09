<main class="w-full relative bg-gradient-to-br from-purple-50 to-pink-50 pt-16 md:pt-20 lg:pt-24">
    <section class="flex flex-col-reverse md:flex-row items-center justify-between gap-8 max-w-[1400px] mx-auto px-6 py-8 md:py-12 lg:py-16">
        <!-- Text Content -->
        <div class="banner-text md:w-1/2">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 md:mb-12 leading-tight">
                Make Money <br />
                Online With the <br />
                Right Skills
            </h1>
            <p class="max-w-md text-base md:text-xl tracking-wide text-gray-500">
                Discover a world of knowledge with our top-notch 
                online course and mentorships. Empower yourself for 
                success in your business career.
            </p>
            <button class="mt-5 bg-gradient-to-r from-[#8a45ec] to-[#5b19f9] text-white px-6 py-3 rounded-xl text-sm md:text-base">
                Make Money Today
            </button>

            <!-- Logos -->
            <div class="flex flex-wrap items-center mt-14 space-x-4">
                <img class="h-8" src="{{ asset('assets/images/google-2015 1.png') }}" alt="Google" />
                <img class="h-8" src="{{ asset('assets/images/udemy-wordmark-1 1.png') }}" alt="Udemy" />
                <img class="h-8" src="{{ asset('assets/images/hub.png') }}" alt="Hub" />
            </div>
        </div>

        <!-- Banner Image -->
        <div class="banner-img md:w-1/2 hidden md:block">
            <img class="w-full max-w-sm md:max-w-md mx-auto" src="{{ asset('assets/images/banner-girl.png') }}" alt="Banner Girl" />
        </div>
    </section>

    <!-- Banner Arrow -->
    <section>
        <img class="hidden md:block absolute top-[350px] left-1/2 -translate-x-1/2" 
             src="{{ asset('assets/images/banner-Arrow_05.png') }}" alt="Arrow" />
    </section>

    <!-- Avatar Group Info Box -->
    <section class="hidden md:block lg:flex absolute md:bottom-8 bottom-[400px] right-1/2 md:right-1/3 lg:right-1/3 translate-x-1/2 md:translate-x-1/2 p-6 bg-white rounded-3xl shadow-lg">
        <div class="grid items-center space-x-4">
            <!-- Avatars -->
            <div class="flex avatar-group -space-x-6">
                <div class="avatar w-12">
                    <img src="https://img.daisyui.com/images/profile/demo/batperson@192.webp" />
                </div>
                <div class="avatar w-12">
                    <img src="https://img.daisyui.com/images/profile/demo/spiderperson@192.webp" />
                </div>
                <div class="avatar w-12">
                    <img src="https://img.daisyui.com/images/profile/demo/spiderperson@192.webp" />
                </div>
                <div class="avatar w-12">
                    <img src="https://img.daisyui.com/images/profile/demo/spiderperson@192.webp" />
                </div>
                <div class="avatar w-12">
                    <img src="https://img.daisyui.com/images/profile/demo/averagebulk@192.webp" />
                </div>
                <div class="avatar avatar-placeholder w-12 bg-[#7649F4] text-white flex items-center justify-center rounded-full">
                    <span>+</span>
                </div>
            </div>
            <span class="text-sm md:text-base">
                More than 5000+ students <br />
                enrolled around the world
            </span>
        </div>
    </section>

    <!-- Floating Star Image -->
    <img class="hidden md:block absolute top-32 left-1/3 w-8 md:w-12" src="{{ asset('assets/images/banner-star.png') }}" alt="Star" />
</main>
