@extends('frontend.layouts.app')

@section('title', 'Terms & Conditions - Skillio')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-5">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ __('trans.terms_conditions_title') }}</h1>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="font-medium">Effective Date:</span>
                    <span>02 Sep 2025</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-medium">Last Updated:</span>
                    <span>01 Sep 2025</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="prose prose-lg max-w-none">
                <!-- Introduction -->
                <div class="mb-8">
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.terms_intro') }}
                        <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">skillio.pro</a> 
                        {{ __('trans.and_any_related_services') }}
                    </p>
                </div>

                <!-- Section 1: Eligibility -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.eligibility_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.eligibility_description') }}
                    </p>
                </div>

                <!-- Section 2: Account Registration -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.account_registration_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.account_registration_description') }}
                    </p>
                </div>

                <!-- Section 3: Payments & Refunds -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.payments_refunds_title') }}</h2>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>{{ __('trans.payment_methods') }}</li>
                        <li>{{ __('trans.no_store_payment_info') }}</li>
                        <li>{{ __('trans.refund_policy') }}</li>
                        <li>{{ __('trans.prices_currency') }}</li>
                    </ul>
                </div>

                <!-- Section 4: User Content -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.user_content_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.user_content_description') }}
                    </p>
                </div>

                <!-- Section 5: Prohibited Conduct -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.prohibited_conduct_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">{{ __('trans.you_agree_not_to') }}</p>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>{{ __('trans.unlawful_purpose') }}</li>
                        <li>{{ __('trans.impersonate_others') }}</li>
                        <li>{{ __('trans.defamatory_content') }}</li>
                        <li>{{ __('trans.interfere_operation') }}</li>
                    </ul>
                </div>

                <!-- Section 6: Intellectual Property -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.intellectual_property_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.intellectual_property_description') }}
                    </p>
                </div>

                <!-- Section 7: Service Modifications -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.service_modifications_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.service_modifications_description') }}
                    </p>
                </div>

                <!-- Section 8: Disclaimers -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.disclaimers_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.disclaimers_description') }}
                    </p>
                </div>

                <!-- Section 9: Limitation of Liability -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.limitation_liability_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.limitation_liability_description') }}
                    </p>
                </div>

                <!-- Section 10: Governing Law -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.governing_law_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.governing_law_description') }}
                    </p>
                </div>

                <!-- Section 11: Contact Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.contact_information_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.contact_information_description') }} 
                        <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a>.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
