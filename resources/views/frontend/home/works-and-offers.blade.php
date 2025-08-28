<section class="mb-20 px-6">
    <section id="how-it-works" class="container mx-auto mb-10">
        <div class="skillio-work-head text-center mb-12">
            <x-section-header title="{{ __('trans.how_it_works_title') }}"
                subtitle="{{ __('trans.how_it_works_subtitle') }}" />
        
            <button class="btn btn-active border-t-cyan-100">
                {{ __('trans.for_learners') }}
            </button>
            <button class="btn btn-active btn-primary">{{ __('trans.for_mentors') }}</button>
        </div>
        <div class="max-w-[1200px] w-full mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-8 gap-x-6 justify-items-center pt-5">
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
