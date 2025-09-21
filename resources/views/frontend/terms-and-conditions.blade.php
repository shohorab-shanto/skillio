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
                    <span>September 10, 2025</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="prose prose-lg max-w-none">
                
                <!-- 1. Acceptance of Terms -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Acceptance of Terms</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>1.1.</strong> By accessing or using <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">Skillio.pro</a> (the "Website"), purchasing any digital product, enrolling in a course, or engaging with our services (collectively, the "Services"), you agree to be bound by these Terms of Service ("Terms").</li>
                        <li><strong>1.2.</strong> If you do not agree to these Terms, you must discontinue use of the Services immediately.</li>
                        <li><strong>1.3.</strong> These Terms constitute a legally binding agreement between you and Skillio LLC ("we," "our," "us").</li>
                        <li><strong>1.4.</strong> These Terms incorporate by reference our Privacy Policy, Refund Policy, and any additional guidelines or notices posted on the Website.</li>
                    </ul>
                </div>

                <!-- 2. Eligibility -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Eligibility</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>2.1.</strong> You must be at least 18 years old to use our Services.</li>
                        <li><strong>2.2.</strong> By using the Website, you represent that you have the legal capacity to enter into these Terms.</li>
                        <li><strong>2.3.</strong> If you are using the Services on behalf of a business entity, you warrant that you are authorized to bind that entity.</li>
                        <li><strong>2.4.</strong> We reserve the right to refuse service or close accounts if eligibility criteria are not met.</li>
                    </ul>
                </div>

                <!-- 3. Services Overview -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Services Overview</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">Skillio LLC provides educational content, templates, digital products, online courses, and consulting in the following categories:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 mb-4">
                        <li><strong>E-Commerce & Online Stores:</strong> Dropshipping, Print on Demand, Amazon FBA, digital products.</li>
                        <li><strong>Agency & Freelance Services:</strong> Ads, marketing, VA, closing, branding, OnlyFans marketing, AI model marketing.</li>
                        <li><strong>Creative & Video:</strong> Editing, YouTube automation, podcasting, design.</li>
                        <li><strong>Technical & AI:</strong> Web development, no-code, automation, SaaS, API, AI workflows.</li>
                        <li><strong>Finance & Trading:</strong> Forex, crypto, NFTs, stock, real estate, personal finance.</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed font-medium">We do not guarantee results, profits, or outcomes from applying our educational material or using our Services.</p>
                </div>

                <!-- 4. Account Registration -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Account Registration</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>4.1.</strong> To access certain Services, you may be required to create an account.</li>
                        <li><strong>4.2.</strong> You agree to provide accurate, complete, and updated information.</li>
                        <li><strong>4.3.</strong> You are responsible for maintaining confidentiality of your account and password.</li>
                        <li><strong>4.4.</strong> Any activities under your account are your responsibility.</li>
                        <li><strong>4.5.</strong> We reserve the right to suspend or delete accounts in case of suspicious, fraudulent, or abusive behavior.</li>
                    </ul>
                </div>

                <!-- 5. Intellectual Property -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Intellectual Property</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>5.1.</strong> All content on the Website, including but not limited to text, videos, images, digital downloads, templates, logos, and branding, is owned by or licensed to Skillio LLC.</li>
                        <li><strong>5.2.</strong> You may not copy, modify, distribute, sell, or exploit our content without prior written permission.</li>
                        <li><strong>5.3.</strong> Purchase of a digital product grants you a personal, non-transferable, non-exclusive license for individual use only.</li>
                        <li><strong>5.4.</strong> Unauthorized resale, sharing, or reproduction of our intellectual property will result in legal action.</li>
                    </ul>
                </div>

                <!-- 6. User Conduct -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">6. User Conduct</h2>
                    <p class="text-gray-700 leading-relaxed mb-4"><strong>6.1.</strong> You agree NOT to use the Website or Services to:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 mb-4">
                        <li>Engage in illegal activities (including money laundering, tax evasion, or fraud).</li>
                        <li>Promote or distribute obscene, defamatory, or hateful content.</li>
                        <li>Copy or resell our courses, templates, or intellectual property.</li>
                        <li>Spam, hack, or interfere with the Website.</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed"><strong>6.2.</strong> We reserve the right to terminate any account or refuse service at our sole discretion if we believe you violated these Terms.</p>
                </div>

                <!-- 7. Payment & Pricing -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Payment & Pricing</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>7.1.</strong> All prices are listed in U.S. Dollars (USD) unless stated otherwise.</li>
                        <li><strong>7.2.</strong> Payment is due at the time of purchase.</li>
                        <li><strong>7.3.</strong> We reserve the right to update pricing at any time without prior notice.</li>
                        <li><strong>7.4.</strong> By purchasing, you authorize us (and our third-party processors like Stripe, PayPal, Wise, etc.) to charge your selected payment method.</li>
                        <li><strong>7.5.</strong> You are responsible for any applicable taxes, VAT, or duties imposed by your jurisdiction.</li>
                    </ul>
                </div>

                <!-- 8. Refund & Cancellation Policy -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Refund & Cancellation Policy</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>8.1. Digital Products & Courses:</strong> All sales are final. Refunds are not provided due to the instant access nature of digital content.</li>
                        <li><strong>8.2. Consulting/Agency Services:</strong> Refunds are only possible if agreed upon in writing before work begins.</li>
                        <li><strong>8.3. Subscription Services:</strong> You may cancel anytime, but payments already processed are non-refundable.</li>
                        <li><strong>8.4.</strong> In rare cases of billing errors, please contact us within 7 days for resolution.</li>
                    </ul>
                </div>

                <!-- 9. Disclaimers -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Disclaimers</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>9.1. No Guarantee of Results:</strong> We provide educational content and resources only. Success in business, e-commerce, marketing, trading, or any related field depends on your individual effort, skills, and market conditions.</li>
                        <li><strong>9.2. Financial Disclaimer:</strong> Nothing on our Website should be construed as financial, legal, or tax advice. Always consult with qualified professionals before making business, trading, or investment decisions.</li>
                        <li><strong>9.3. Trading & Investment Risk:</strong> Forex, cryptocurrency, NFT, and stock market activities carry significant risks. You may lose some or all of your investment.</li>
                        <li><strong>9.4. No Professional Advice:</strong> Content related to OnlyFans, AI models, marketing, or branding is provided for informational purposes only.</li>
                        <li><strong>9.5. Technical Disclaimer:</strong> We do not warrant uninterrupted availability of the Website. Downtime, bugs, or third-party outages may occur.</li>
                    </ul>
                </div>

                <!-- 10. Third-Party Services -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Third-Party Services</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>10.1.</strong> We may recommend or link to third-party tools (e.g., Shopify, Amazon, Zapier, PayPal, Stripe).</li>
                        <li><strong>10.2.</strong> We are not responsible for third-party terms, reliability, or actions.</li>
                        <li><strong>10.3.</strong> Use of third-party platforms is entirely at your own risk.</li>
                    </ul>
                </div>

                <!-- 11. Confidentiality & Non-Disclosure -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Confidentiality & Non-Disclosure</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>11.1.</strong> If you engage us for agency or consulting work, you agree not to disclose confidential strategies, documents, or methods provided to you.</li>
                        <li><strong>11.2.</strong> We equally agree to keep your sensitive business information confidential.</li>
                        <li><strong>11.3.</strong> Exceptions apply where disclosure is required by law or regulatory authorities.</li>
                    </ul>
                </div>

                <!-- 12. Limitation of Liability -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Limitation of Liability</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>12.1.</strong> To the fullest extent permitted by law, Skillio LLC is not liable for any direct, indirect, incidental, consequential, or punitive damages arising from your use of our Services.</li>
                        <li><strong>12.2.</strong> We are not responsible for losses related to business decisions, trading outcomes, or misuse of provided information.</li>
                        <li><strong>12.3.</strong> Our total liability shall not exceed the amount you paid for the Services.</li>
                    </ul>
                </div>

                <!-- 13. Indemnification -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Indemnification</h2>
                    <p class="text-gray-700 leading-relaxed">
                        You agree to indemnify and hold harmless Skillio LLC, its employees, affiliates, and contractors from any claims, liabilities, damages, or expenses resulting from your use of the Services or violation of these Terms.
                    </p>
                </div>

                <!-- 14. Termination -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">14. Termination</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>14.1.</strong> We may suspend or terminate your account at any time for violation of these Terms.</li>
                        <li><strong>14.2.</strong> Upon termination, your access to purchased content remains limited to what was granted under license at the time of purchase.</li>
                        <li><strong>14.3.</strong> We may also terminate Services if required by law or regulatory restrictions.</li>
                    </ul>
                </div>

                <!-- 15. Governing Law & Jurisdiction -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">15. Governing Law & Jurisdiction</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>15.1.</strong> These Terms are governed by the laws of the State of Wyoming, United States.</li>
                        <li><strong>15.2.</strong> Any disputes shall be resolved exclusively in courts located in Sheridan County, Wyoming.</li>
                        <li><strong>15.3.</strong> You waive any objection to such jurisdiction on grounds of inconvenient forum.</li>
                    </ul>
                </div>

                <!-- 16. Force Majeure -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">16. Force Majeure</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We shall not be held liable for failure or delay in performance due to events beyond our reasonable control, including but not limited to natural disasters, government actions, internet outages, strikes, or pandemics.
                    </p>
                </div>

                <!-- 17. Assignment -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">17. Assignment</h2>
                    <p class="text-gray-700 leading-relaxed">
                        You may not assign or transfer your rights or obligations under these Terms without our prior written consent. We may assign these Terms in connection with a merger, acquisition, or sale of assets.
                    </p>
                </div>

                <!-- 18. Entire Agreement -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">18. Entire Agreement</h2>
                    <p class="text-gray-700 leading-relaxed">
                        These Terms, together with our Privacy Policy and other referenced documents, constitute the entire agreement between you and Skillio LLC.
                    </p>
                </div>

                <!-- 19. Changes to Terms -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">19. Changes to Terms</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>19.1.</strong> We may update these Terms at any time.</li>
                        <li><strong>19.2.</strong> Continued use of the Website after updates constitutes acceptance of revised Terms.</li>
                        <li><strong>19.3.</strong> The last updated date will always be shown at the top.</li>
                    </ul>
                </div>

                <!-- 20. Contact Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">20. Contact Information</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">For questions or concerns about these Terms, contact us at:</p>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <p class="font-semibold text-gray-900">Skillio LLC</p>
                        <p class="text-gray-700">30 N Gould St, Sheridan, WY 82801</p>
                        <p class="text-gray-700 mt-2">
                            <strong>Email:</strong> 
                            <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700">info@skillio.pro</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
