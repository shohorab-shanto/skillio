@extends('frontend.layouts.app')

@section('title', 'Terms & Conditions - Skillio')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-5">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Terms & Conditions</h1>
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
                        Welcome to Skillio LLC. These Terms & Conditions govern your use of our website 
                        <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">skillio.pro</a> 
                        and any related services. By using our Services, you agree to be bound by these Terms. 
                        If you do not agree, do not use our Services.
                    </p>
                </div>

                <!-- Section 1: Eligibility -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Eligibility</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Our Services are available worldwide. There are no age restrictions; however, if you are under the age of
                        majority in your jurisdiction, you must have the consent of a parent or guardian.
                    </p>
                </div>

                <!-- Section 2: Account Registration -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Account Registration</h2>
                    <p class="text-gray-700 leading-relaxed">
                        To access certain features, you must create an account and provide accurate, current, and complete
                        information. You are responsible for maintaining the confidentiality of your login credentials and for all
                        activities under your account.
                    </p>
                </div>

                <!-- Section 3: Payments & Refunds -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Payments & Refunds</h2>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>Payments can be made via Apple Pay, Google Pay, or credit/debit card.</li>
                        <li>We do not store credit card or payment information; transactions are processed securely through third-party payment processors.</li>
                        <li>Refunds will only be granted if the booked mentor did not attend the education session for which the user paid. Refund requests must be submitted within 7 days of the scheduled session.</li>
                        <li>All prices are listed in USD and EUR.</li>
                    </ul>
                </div>

                <!-- Section 4: User Content -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">4. User Content</h2>
                    <p class="text-gray-700 leading-relaxed">
                        You may submit comments, reviews, and other materials to our platform. You grant us a worldwide,
                        non-exclusive, royalty-free license to use, reproduce, and display your User Content in connection with
                        our Services. You are solely responsible for the legality and appropriateness of your submissions.
                    </p>
                </div>

                <!-- Section 5: Prohibited Conduct -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Prohibited Conduct</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">You agree not to:</p>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>Use the Services for any unlawful purpose</li>
                        <li>Impersonate others or provide false information</li>
                        <li>Post content that is defamatory, obscene, abusive, or violates others' rights</li>
                        <li>Interfere with the operation of the Website or Services</li>
                    </ul>
                </div>

                <!-- Section 6: Intellectual Property -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Intellectual Property</h2>
                    <p class="text-gray-700 leading-relaxed">
                        All materials, trademarks, and content on our Website are the property of Skillio LLC or our licensors and
                        may not be used without permission.
                    </p>
                </div>

                <!-- Section 7: Service Modifications -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Service Modifications</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We reserve the right to modify or discontinue any part of the Services at any time without notice.
                    </p>
                </div>

                <!-- Section 8: Disclaimers -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Disclaimers</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Our Services are provided on an "AS AVAILABLE" basis. We do not guarantee uninterrupted or error-free
                        service. We make no warranties regarding the accuracy or completeness of educational content.
                    </p>
                </div>

                <!-- Section 9: Limitation of Liability -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Limitation of Liability</h2>
                    <p class="text-gray-700 leading-relaxed">
                        To the maximum extent permitted by law, Skillio LLC shall not be liable for any indirect, incidental, or
                        consequential damages arising from your use of the Services.
                    </p>
                </div>

                <!-- Section 10: Governing Law -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Governing Law</h2>
                    <p class="text-gray-700 leading-relaxed">
                        These Terms are governed by the laws of the State of Wyoming, USA, without regard to conflict of laws
                        principles.
                    </p>
                </div>

                <!-- Section 11: Contact Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Contact Information</h2>
                    <p class="text-gray-700 leading-relaxed">
                        For questions, email us at 
                        <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a>.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
