
<section class="px-6">

    <section id="how-it-works" class="container mx-auto">
    
        <div class="skillio-work-head text-center mb-12">
       
            <x-section-header title="{{ __('trans.how_it_works_title') }}"
                subtitle="{{ __('trans.how_it_works_subtitle') }}" />
                 
        
            <div class="inline-flex gap-4 justify-center mb-6">
                <button
                    id="learners-btn"
                    type="button"
                    class="btn btn-normal-case border-t-cyan-100 tab-btn"
                    style="text-transform: none !important;"
                    onclick="showTab('for_learners', this)"
                >
                    {{ __('trans.for_members') }}
                </button>
                <button
                    id="mentors-btn"
                    type="button"
                    class="btn btn-normal-case btn-primary tab-btn"
                    style="text-transform: none !important;"
                    onclick="showTab('for_mentors', this)"
                >
                    {{ __('trans.for_mentors') }}
                </button>
            </div>
            <script>
                function showTab(tabId, btn) {
                    var learners = document.getElementById('for_learners');
                    var mentors = document.getElementById('for_mentors');
                    var tabBtns = document.querySelectorAll('.tab-btn');
                    
                    if (tabId == 'for_learners') {
                        learners.style.display = 'grid';
                        mentors.style.display = 'none';
                    } else {
                        learners.style.display = 'none';
                        mentors.style.display = 'grid';
                    }
                    
                    tabBtns.forEach(function(b) {
                        b.classList.remove('btn-primary');
                        b.classList.remove('btn-active');
                    });
                    btn.classList.add('btn-primary');
                    btn.classList.add('btn-active');
                }
                
                // Set initial state
                document.addEventListener('DOMContentLoaded', function() {
                    showTab('for_learners', document.getElementById('learners-btn'));
                    
                    // Force text transform for all buttons
                    const buttons = document.querySelectorAll('.btn');
                    buttons.forEach(button => {
                        button.style.textTransform = 'none';
                    });
                });
            </script>
        </div>
        <div id="for_learners" class="max-w-[1200px] w-full mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-8 gap-x-6 justify-items-center pt-5">
            <!-- Card Items 01 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <svg class="w-14 h-14 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.learners_card_1_title') }}</h2>
                    <p>
                        {{ __('trans.learners_card_1_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 02 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <svg class="w-14 h-14 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">
                        {{ __('trans.learners_card_2_title') }}
                    </h2>
                    <p>
                        {{ __('trans.learners_card_2_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 03 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <svg class="w-14 h-14 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.learners_card_3_title') }}</h2>
                    <p>
                        {{ __('trans.learners_card_3_description') }}
                    </p>
                </div>
            </div>
        </div>
        <div id="for_mentors" class="max-w-[1200px] w-full mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-8 gap-x-6 justify-items-center pt-5">
            <!-- Card Items 01 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/frame1.png') }}" alt="right" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.card_1_title') }}</h2>
                    <p>
                        {{ __('trans.card_1_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 02 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/fram2.png') }}" alt="people" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">
                        {{ __('trans.card_2_title_line1') }} <br />
                        {{ __('trans.card_2_title_line2') }}
                    </h2>
                    <p>
                        {{ __('trans.card_2_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 03 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/frame3.png') }}" alt="earn" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.card_3_title') }}</h2>
                    <p>
                        {{ __('trans.card_3_description') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section id="what-we-offer">
        <x-section-header 
            title="{{ __('trans.what_we_offer_title') }}" 
            subtitle="{{ __('trans.what_we_offer_subtitle') }}" 
            class="pt-24 mb-9"
        />
        <div class="max-w-[1200px] w-full mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-8 gap-x-6 justify-items-center pt-5">
            <!-- Card items 01 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/SD-1.png') }}" alt="Shoes" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.offer_1_title') }}</h2>
                    <p class="text-center mb-9">
                        {{ __('trans.offer_1_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 02 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/SD-2.png') }}" alt="Shoes" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.offer_2_title') }}</h2>
                    <p class="text-center mb-9">
                        {{ __('trans.offer_2_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 03 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/SD-3.png') }}" alt="Shoes" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.offer_3_title') }}</h2>
                    <p class="text-center mb-9">
                        {{ __('trans.offer_3_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 04 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/SD-4.png') }}" alt="Shoes" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.offer_4_title') }}</h2>
                    <p class="text-center mb-9">
                        {{ __('trans.offer_4_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 05 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/SD-5.png') }}" alt="Shoes" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.offer_5_title') }}</h2>
                    <p class="text-center mb-9">
                        {{ __('trans.offer_5_description') }}
                    </p>
                </div>
            </div>
            <!-- Card items 06 -->
            <div class="card bg-base-100 w-full shadow-sm">
                <figure class="px-10 pt-10">
                    <img src="{{ asset('assets/images/SD-6.png') }}" alt="Shoes" class="rounded-xl" />
                </figure>
                <div class="card-body items-center text-center">
                    <h2 class="card-title">{{ __('trans.offer_6_title') }}</h2>
                    <p class="text-center mb-9">{{ __('trans.offer_6_description') }}</p>
                </div>
            </div>
        </div>
    </section>
</section>
