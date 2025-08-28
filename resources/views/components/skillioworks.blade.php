<section  class="mb-20">
            <section class="container mx-auto mb-10">
                <div class="skillio-work-head text-center mb-12">
                    <x-section-header title="{{ __('trans.how_skillio_works') }}"
            subtitle="{{ __('trans.skillio_structured_simple') }}" />
        
                    <button class="btn btn-active border-t-cyan-100">
                        {{ __('trans.for_learners') }}
                    </button>
                    <button class="btn btn-active btn-primary">{{ __('trans.for_mentors') }}</button>
                </div>
                <div class="w-[1200px] mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3  gap-y-8 gap-x-6.5 justify-between  pt-5">
                    <!-- Card Items 01 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img  src="{{ asset('assets/images/frame1.png') }}" alt="right" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">{{ __('trans.register_get_verified') }}</h2>
                            <p>
                                {{ __('trans.personalized_quiz_description') }}
                            </p>
                        </div>
                    </div>
                    <!-- Card items 02 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img src="{{ asset('assets/images/fram2.png') }}" alt="people" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">
                                {{ __('trans.publish_courses_set_timeslots') }}
                            </h2>
                            <p>
                                {{ __('trans.personalized_quiz_description') }}
                            </p>
                        </div>
                    </div>
                    <!-- Card items 03 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img src="{{ asset('assets/images/frame3.png') }}" alt="earn" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">{{ __('trans.earn_inspire') }}</h2>
                            <p>
                                {{ __('trans.personalized_quiz_description') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="skillio-different-head">
                    <h1 class="text-6xl font-bold text-center mb-8 pt-24">
                        {{ __('trans.what_does_skillio_offer') }}
                    </h1>
                    <p class="text-center mb-9">
                        {{ __('trans.skillio_connects_description') }}
                    </p>
                </div>
                <div class="w-[1200px] mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3  gap-y-8 gap-x-6.5 justify-between  pt-5">
                    <!-- Card items 01 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img src="{{ asset('assets/images/SD-1.png') }}" alt="Shoes" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">{{ __('trans.verified_mentors') }}</h2>
                            <p class="text-center mb-9">
                                {{ __('trans.only_proven_professionals') }}
                            </p>
                        </div>
                    </div>
                    <!-- Card items 02 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img src="{{ asset('assets/images/SD-2.png') }}" alt="Shoes" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">{{ __('trans.personalized_recommendations') }}</h2>
                            <p class="text-center mb-9">
                                {{ __('trans.course_mentoring_suggestions') }}
                            </p>
                        </div>
                    </div>
                    <!-- Card items 03 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img src="{{ asset('assets/images/SD-3.png') }}" alt="Shoes" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">{{ __('trans.online_inperson_options') }}</h2>
                            <p class="text-center mb-9">
                                {{ __('trans.learn_anywhere_flexible') }}
                            </p>
                        </div>
                    </div>
                    <!-- Card items 04 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img src="{{ asset('assets/images/SD-4.png') }}" alt="Shoes" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">{{ __('trans.secure_inapp_chat') }}</h2>
                            <p class="text-center mb-9">
                                {{ __('trans.stay_securely_connected') }}
                            </p>
                        </div>
                    </div>
                    <!-- Card items 05 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img src="{{ asset('assets/images/SD-5.png') }}" alt="Shoes" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">{{ __('trans.quality_controlled_content') }}</h2>
                            <p class="text-center mb-9">
                                {{ __('trans.every_lesson_meets_standards') }}
                            </p>
                        </div>
                    </div>
                    <!-- Card items 06 -->
                    <div class="card bg-base-100 w-[400px] shadow-sm">
                        <figure class="px-10 pt-10">
                            <img src="{{ asset('assets/images/SD-6.png') }}" alt="Shoes" class="rounded-xl" />
                        </figure>
                        <div class="card-body items-center text-center">
                            <h2 class="card-title">{{ __('trans.lifetime_access') }}</h2>
                            <p class="text-center mb-9">{{ __('trans.learn_at_your_pace_forever') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </section>