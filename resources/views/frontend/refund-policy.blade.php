@extends('frontend.layouts.app')

@section('title', 'Refund & Return Policy - Skillio')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-5">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Refund & Return Policy</h1>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="font-medium">Effective Date:</span>
                    <span>September 10, 2025</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-medium">Last Updated:</span>
                    <span>September 10, 2025</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            
            <!-- Section 1: General Policy -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. General Policy</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>1.1.</strong> Skillio LLC ("we," "our," "us") provides digital products, online courses, templates, software tools, consulting, and agency services.</p>
                    <p><strong>1.2.</strong> Due to the intangible and irrevocable nature of digital goods and consulting services, all sales are final unless explicitly stated otherwise in writing.</p>
                    <p><strong>1.3.</strong> By completing a purchase on <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">Skillio.pro</a>, you acknowledge, accept, and agree to the terms of this Refund & Return Policy.</p>
                    <p><strong>1.4.</strong> This Policy is intended to protect both the integrity of our products and fairness toward all customers.</p>
                </div>
            </div>

            <!-- Section 2: Digital Products -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Digital Products (Courses, Templates, eBooks, Software Tools)</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>2.1.</strong> All sales of digital products are final and non-refundable once access has been granted or the download link delivered.</p>
                    <p><strong>2.2.</strong> We do not accept returns or refunds for digital files, as delivery is permanent and cannot be revoked.</p>
                    <p><strong>2.3.</strong> Exceptions may apply only at our sole discretion in the following circumstances:</p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Duplicate purchase (accidental double payment for the same item).</li>
                        <li>Technical issues preventing access to the purchased product, verified as a fault on our side (not related to your device, internet connection, or software).</li>
                        <li>Incorrect product delivery due to an error on our end.</li>
                    </ul>
                </div>
            </div>

            <!-- Section 3: Consulting, Coaching & Agency Services -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Consulting, Coaching & Agency Services</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>3.1.</strong> Payments for consulting, coaching, or agency services are non-refundable once work has begun.</p>
                    <p><strong>3.2.</strong> If you cancel before we commence work, you may request a partial refund, subject to an administrative deduction of up to 20%.</p>
                    <p><strong>3.3.</strong> Once deliverables have been provided (e.g., calls, strategies, ad campaigns, reports, creative materials), no refunds will be issued.</p>
                    <p><strong>3.4.</strong> Prepaid packages (blocks of consulting hours or campaigns) are non-transferable and must be used within the agreed time frame.</p>
                </div>
            </div>

            <!-- Section 4: Subscription Services -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Subscription Services</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>4.1.</strong> Subscription fees (monthly, quarterly, yearly) are non-refundable once charged.</p>
                    <p><strong>4.2.</strong> You may cancel your subscription at any time, but cancellation will only apply to future billing cycles.</p>
                    <p><strong>4.3.</strong> Refunds are not issued for unused periods or partial billing cycles.</p>
                    <p><strong>4.4.</strong> Free trials (if offered) must be cancelled before the trial ends to avoid charges.</p>
                </div>
            </div>

            <!-- Section 5: Chargebacks & Disputes -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Chargebacks & Disputes</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>5.1.</strong> Initiating a chargeback without contacting us first is considered a violation of this Policy.</p>
                    <p><strong>5.2.</strong> Chargebacks may result in:</p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Permanent account suspension.</li>
                        <li>Revocation of access to all previously purchased content.</li>
                        <li>Collection of outstanding fees and costs.</li>
                    </ul>
                    <p><strong>5.3.</strong> We reserve the right to dispute chargebacks with supporting evidence such as:</p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Download/access logs.</li>
                        <li>Email confirmations.</li>
                        <li>Timestamped proof of service delivery.</li>
                    </ul>
                </div>
            </div>

            <!-- Section 6: No Guarantee of Results -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. No Guarantee of Results</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>6.1.</strong> Our products and services are intended for educational and informational purposes only.</p>
                    <p><strong>6.2.</strong> We make no warranties regarding your ability to achieve specific results, revenue, or outcomes by applying our content.</p>
                    <p><strong>6.3.</strong> Lack of results, dissatisfaction, or personal circumstances are not valid grounds for a refund.</p>
                </div>
            </div>

            <!-- Section 7: Exceptional Circumstances -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Exceptional Circumstances</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>7.1.</strong> In rare cases, and at our sole discretion, we may issue a partial or full refund.</p>
                    <p><strong>7.2.</strong> Approval of one refund does not constitute a waiver of our no-refund policy for future purchases.</p>
                    <p><strong>7.3.</strong> Refunds, if granted, may be subject to processing and administrative fees.</p>
                </div>
            </div>

            <!-- Section 8: EU/UK Consumer Rights -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. EU/UK Consumer Rights (if applicable)</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>8.1.</strong> If you are a resident of the EU/UK, you may have additional statutory rights under local consumer protection laws.</p>
                    <p><strong>8.2.</strong> Under EU Directive 2011/83/EU on consumer rights, digital content delivered immediately upon purchase is exempt from the standard 14-day withdrawal right, provided you gave prior consent to instant access.</p>
                    <p><strong>8.3.</strong> By purchasing our digital products, you expressly agree that delivery begins immediately and you waive your right to cancellation once access is provided.</p>
                </div>
            </div>

            <!-- Section 9: How to Request a Refund -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. How to Request a Refund (If Eligible)</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>9.1.</strong> To request a refund under eligible conditions, you must email us at 
                        <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a> with:
                    </p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>Order number.</li>
                        <li>Date of purchase.</li>
                        <li>Detailed explanation of the issue.</li>
                    </ul>
                    <p><strong>9.2.</strong> Requests must be submitted within 7 calendar days of purchase.</p>
                    <p><strong>9.3.</strong> Approved refunds will be processed within 10–15 business days to the original payment method.</p>
                </div>
            </div>

            <!-- Section 10: Policy Updates -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Policy Updates</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>10.1.</strong> We reserve the right to amend or update this Refund & Return Policy at any time.</p>
                    <p><strong>10.2.</strong> Updates will be posted on this page with a revised "Effective Date."</p>
                    <p><strong>10.3.</strong> Continued use of our Website and Services after updates constitutes acceptance of the revised Policy.</p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-8">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="space-y-2 text-gray-700">
                        <p><strong>Skillio LLC</strong></p>
                        <p>30 N Gould St, Sheridan, WY 82801</p>
                        <p>Email: <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
