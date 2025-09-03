<footer class="max-w-[1440px] w-full mx-auto tracking-wide bg-[#6E3FF3] rounded-[40px] px-4 sm:px-6 lg:px-8 py-10 text-white">
    <div class=" space-y-10">

        <!-- Top Section: Contact | Subscribe | Social -->
        <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-5 md:gap-10">

            <!-- Left: Contact Us -->
            <div class="flex items-center h-auto md:h-48">
                <div class="flex flex-col items-center justify-center space-y-0 md:space-y-2 md:pr-20 w-full ml-0 md:ml-[50px]">
                    <p class="text-base text-center w-full mb-1 md:mb-0">{{ __('trans.contact_us') }}</p>
                    <a href="mailto:info@skillio.pro" class="text-base cursor-pointer hover:underline text-center w-full">
                        info@skillio.pro
                    </a>
                </div>
                <div class="h-auto md:h-48 md:border-r border-white/30 min-h-[80px] md:min-h-[120px]"></div>
            </div>




            <!-- Center: Subscribe (All Content Center Aligned) -->
            <div class="flex flex-col items-center justify-center text-center space-y-4 text-white max-w-md w-full mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold md:tracking-wide w-full text-center">
                    {{ __('trans.subscribe_to_newsletter') }}
                </h2>
                <p class="text-white/80 text-sm md:text-base w-full text-center">
                    {{ __('trans.newsletter_description') }}
                </p>
                <form class="flex flex-col sm:flex-row gap-2 w-full max-w-sm mx-auto justify-center items-center" autocomplete="off">
                    <input 
                        type="email" 
                        placeholder="{{ __('trans.enter_your_email') }}"
                        class="flex-1 px-4 py-2 rounded-lg bg-white/90 text-gray-800 placeholder-gray-500 outline-none focus:ring-2 focus:ring-white/40 transition-all duration-200 text-sm text-center" 
                        required 
                        aria-label="Email address"
                    />
                    <button 
                        type="submit"
                        class="bg-white text-[#6E3FF3] px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-200 whitespace-nowrap text-sm border border-white/30 text-center"
                    >
                        {{ __('trans.subscribe') }}
                    </button>
                </form>
            </div>

            <!-- Right: Social Media -->
            <div class="flex items-center h-auto md:h-48">
                <div class="h-auto md:h-48 md:border-r border-white/30 min-h-[60px] md:min-h-[120px]"></div>
                <div class="flex flex-col items-center md:items-end space-y-0 md:space-y-2 md:pl-10 mr-0 md:mr-[50px]">
                    <p class="text-base text-center w-full">{{ __('trans.social_media') }}</p>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-4">
                        <!-- TikTok -->
                        <a href="https://www.tiktok.com/@skillio.official?_t=ZS-8zKoEUxHhEP&_r=1" aria-label="TikTok" class="hover:text-gray-300" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12.93 2h2.96c.21 1.31.87 2.5 1.85 3.38a6.54 6.54 0 0 0 3.24 1.48v3.02a9.49 9.49 0 0 1-5.1-1.44v7.69a6.86 6.86 0 0 1-2.03 4.94A7 7 0 0 1 7.1 22 7.01 7.01 0 0 1 4 19.85a7.09 7.09 0 0 1-.7-3.14A7.06 7.06 0 0 1 7.08 10c.29 0 .57.02.85.06v3.14a3.98 3.98 0 0 0-1.1-.15 3.08 3.08 0 0 0-3.03 3.17A3.1 3.1 0 0 0 7.09 20a3.18 3.18 0 0 0 2.26-.94 3.2 3.2 0 0 0 .94-2.25V2h2.64Z">
                                </path>
                            </svg>
                        </a>
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/share/1CbSrHVvcG/?mibextid=wwXIfr" aria-label="Facebook" class="hover:text-gray-300" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M13.5 22v-8h2.85l.43-3.3H13.5V8.43c0-.96.27-1.61 1.65-1.61h1.76V4.02A23.1 23.1 0 0 0 13.98 4c-2.42 0-4.08 1.48-4.08 4.2v2.5H7.5v3.3h2.4v8h3.6Z">
                                </path>
                            </svg>
                        </a>
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/skillio.official?igsh=OHFsbTdjaXl5YTZp&utm_source=qr" aria-label="Instagram" class="hover:text-gray-300" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M7 2C4.79 2 3 3.79 3 6v12c0 2.21 1.79 4 4 4h10c2.21 0 4-1.79 4-4V6c0-2.21-1.79-4-4-4H7Zm0 2h10c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H7a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2Zm5 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm4.5-.75a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z">
                                </path>
                            </svg>
                        </a>
                        <!-- YouTube -->
                        <a href="https://www.youtube.com/@skillio.official" aria-label="YouTube" class="hover:text-gray-300" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M10 15.5 15 12l-5-3.5v7ZM4 6.72C4 4.66 5.66 3 7.72 3h8.56C18.34 3 20 4.66 20 6.72v10.56A3.72 3.72 0 0 1 16.28 21H7.72A3.72 3.72 0 0 1 4 17.28V6.72Z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle: Logo and Navigation Links -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-y-6">
            <div class="flex flex-col items-center md:items-start">
                <h1 class="text-5xl font-bold">Skillio</h1>
            </div>
            <div class="flex flex-1 flex-col md:flex-row w-full">
                <!-- Part 1: Center Aligned Links -->
                <div class="flex flex-wrap justify-center items-center gap-6 text-sm font-medium flex-1">
                    <a href="{{ url('/#why-we-are-different') }}" class="hover:underline">{{ __('trans.about_us_footer') }}</a>
                    <a href="{{ url('/#how-it-works') }}" class="hover:underline">{{ __('trans.how_it_works_footer') }}</a>
                    <a href="{{ url('/#unleash-your-potential') }}" class="hover:underline">{{ __('trans.benefits') }}</a>
                    <a href="{{ route('mentors') }}" class="hover:underline">{{ __('trans.mentors_footer') }}</a>
                    <a href="{{ url('/#testimonials') }}" class="hover:underline">{{ __('trans.testimonials_footer') }}</a>
                    <a href="{{ url('/#faq') }}" class="hover:underline">{{ __('trans.faq_footer') }}</a>
                </div>
                                 <!-- Part 2: Right Aligned Links -->
                 <div class="flex flex-wrap justify-center md:justify-end items-center gap-6 text-sm font-medium mt-4 md:mt-0 md:ml-8">
                     <a href="{{ route('terms-and-conditions') }}" class="hover:underline">{{ __('trans.terms_conditions') }}</a>
                     <a href="{{ route('privacy-policy') }}" class="hover:underline">{{ __('trans.privacy_policy') }}</a>
                 </div>
            </div>
        </div>

        <!-- Bottom: Copyright & Payments -->
        <div class="border-t border-white/30 pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-y-4">
                <p class="text-sm text-white/80">{{ __('trans.copyright') }}</p>
                <div class="flex flex-wrap justify-center gap-4 items-center">
                    <img src="{{ asset('assets/images/klarna.png') }}" alt="Klarna" class="h-5" />
                    <img src="{{ asset('assets/images/visa.png') }}" alt="Visa" class="h-5" />
                    <img src="{{ asset('assets/images/paypal.png') }}" alt="PayPal" class="h-5" />
                    <img src="{{ asset('assets/images/amex.png') }}" alt="Amex" class="h-5" />
                    <img src="{{ asset('assets/images/discover.png') }}" alt="Discover" class="h-5" />
                    <img src="{{ asset('assets/images/master-card.png') }}" alt="Mastercard" class="h-5" />
                </div>
            </div>
        </div>

    </div>
</footer>
