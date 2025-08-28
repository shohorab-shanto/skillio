@extends('frontend.layouts.app')

@section('title', 'Privacy Policy - Skillio')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-5">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ __('trans.privacy_policy_title') }}</h1>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="font-medium">{{ __('trans.effective_date') }}</span>
                    <span>02 Sep 2025</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-medium">{{ __('trans.last_updated') }}</span>
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
                        {{ __('trans.privacy_intro') }}
                        <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">skillio.pro</a> 
                        {{ __('trans.and_related_services') }}
                    </p>
                </div>

                <!-- Section 1: Information We Collect -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.information_we_collect_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">{{ __('trans.we_collect') }}</p>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li><strong>{{ __('trans.account_information') }}</strong> {{ __('trans.account_info_details') }}</li>
                        <li><strong>{{ __('trans.payment_information') }}</strong> {{ __('trans.payment_info_details') }}</li>
                        <li><strong>{{ __('trans.user_content') }}</strong> {{ __('trans.user_content_details') }}</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        {{ __('trans.no_cookies_notice') }}
                    </p>
                </div>

                <!-- Section 2: How We Use Your Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.how_we_use_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">{{ __('trans.we_use_your_info_to') }}</p>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>{{ __('trans.create_manage_account') }}</li>
                        <li>{{ __('trans.provide_educational_services') }}</li>
                        <li>{{ __('trans.process_payments_refunds') }}</li>
                        <li>{{ __('trans.communicate_updates') }}</li>
                    </ul>
                </div>

                <!-- Section 3: Sharing of Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.sharing_info_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        {{ __('trans.no_sell_data') }}
                    </p>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>{{ __('trans.payment_processors') }}</li>
                        <li>{{ __('trans.service_providers') }}</li>
                        <li>{{ __('trans.law_enforcement') }}</li>
                    </ul>
                </div>

                <!-- Section 4: Data Storage & Security -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.data_storage_security_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.data_storage_description') }}
                    </p>
                </div>

                <!-- Section 5: International Users -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.international_users_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.international_users_description') }}
                    </p>
                </div>

                <!-- Section 6: Your Rights -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.your_rights_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.your_rights_description') }}
                        <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a> 
                        {{ __('trans.to_make_requests') }}
                    </p>
                </div>

                <!-- Section 7: Children's Privacy -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.children_privacy_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.children_privacy_description') }}
                    </p>
                </div>

                <!-- Section 8: Changes to This Policy -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.changes_policy_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed">
                        {{ __('trans.changes_policy_description') }}
                    </p>
                </div>

                <!-- Section 9: Contact Us -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('trans.contact_us_title') }}</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        {{ __('trans.contact_us_description') }}
                    </p>
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="text-gray-700 leading-relaxed">
                            <p class="font-semibold">{{ __('trans.skillio_llc') }}</p>
                            <p>{{ __('trans.skillio_address_line1') }}</p>
                            <p>{{ __('trans.skillio_address_line2') }}</p>
                            <p class="mt-2">
                                {{ __('trans.email') }} 
                                <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
