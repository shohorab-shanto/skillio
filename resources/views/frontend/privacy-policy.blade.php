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
                    <span>September 10, 2025</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            <div class="prose prose-lg max-w-none">
                
                <!-- 1. Introduction -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Introduction</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Skillio LLC ("we," "our," "us") respects your privacy and is committed to protecting your personal data. This Privacy Policy explains how we collect, use, disclose, and safeguard information when you use our Website and Services, including online courses, digital products, templates, consulting, and agency services.
                    </p>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        By using our website, you consent to the practices described in this Privacy Policy. If you do not agree, you must discontinue use of the Services.
                    </p>
                </div>

                <!-- 2. Information We Collect -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Information We Collect</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">We may collect the following categories of information:</p>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">2.1. Information You Provide Voluntarily</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 mb-4">
                        <li>Full name, email address, billing address, phone number.</li>
                        <li>Payment details (processed securely by third-party processors).</li>
                        <li>Account login credentials (if you create an account).</li>
                        <li>Content you upload or share (assignments, messages, reviews, testimonials, user-generated content).</li>
                        <li>Communication preferences (newsletter opt-in, marketing consents).</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">2.2. Information Collected Automatically</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 mb-4">
                        <li>IP address, browser type, operating system, device identifiers.</li>
                        <li>Cookies, tracking pixels, analytics data.</li>
                        <li>Website usage data (pages visited, clicks, time spent, referral URLs).</li>
                        <li>Log files including timestamp, session ID, access errors.</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">2.3. Information from Third Parties</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li>Payment processors (Stripe, PayPal, Wise).</li>
                        <li>Advertising platforms (Meta Ads, Google Ads, TikTok Ads).</li>
                        <li>Social media platforms if you use third-party login.</li>
                        <li>Affiliates and referral partners who direct you to our website.</li>
                    </ul>
                </div>

                <!-- 3. How We Use Your Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. How We Use Your Information</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">We process collected data for purposes including:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li>To provide, personalize, and improve Services.</li>
                        <li>To process transactions and deliver purchased products.</li>
                        <li>To send transactional emails (confirmations, invoices, account updates).</li>
                        <li>To provide customer support and respond to inquiries.</li>
                        <li>To send promotional emails or newsletters (if you opt-in).</li>
                        <li>To conduct analytics, improve functionality, and monitor performance.</li>
                        <li>To comply with legal and regulatory obligations (e.g., tax, accounting).</li>
                        <li>To enforce our Terms of Service and protect against fraud.</li>
                    </ul>
                </div>

                <!-- 4. Legal Basis for Processing (GDPR) -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Legal Basis for Processing (GDPR)</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">For users in the EU/EEA, our legal bases for processing include:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>Contractual necessity:</strong> Providing purchased products/services.</li>
                        <li><strong>Consent:</strong> Marketing communications, optional cookies.</li>
                        <li><strong>Legal obligation:</strong> Record-keeping, tax compliance.</li>
                        <li><strong>Legitimate interest:</strong> Improving our services, preventing fraud.</li>
                    </ul>
                </div>

                <!-- 5. Cookies & Tracking Technologies -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Cookies & Tracking Technologies</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.1. Usage</h3>
                    <p class="text-gray-700 leading-relaxed mb-4">We use cookies and similar technologies (pixels, beacons, scripts) to:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 mb-4">
                        <li>Remember user preferences.</li>
                        <li>Analyze Website performance and traffic.</li>
                        <li>Personalize advertising and remarketing campaigns.</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.2. Control</h3>
                    <p class="text-gray-700 leading-relaxed">
                        You can control or disable cookies in your browser. Some features may not work correctly if cookies are disabled.
                    </p>
                </div>

                <!-- 6. Sharing & Disclosure -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Sharing & Disclosure</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">We do not sell or rent your personal data. We may share information with:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li><strong>Service Providers:</strong> Payment processors, hosting, email, analytics.</li>
                        <li><strong>Legal Authorities:</strong> If required by law, subpoena, or regulatory request.</li>
                        <li><strong>Business Transfers:</strong> In the event of a merger, acquisition, or sale.</li>
                        <li><strong>Marketing Partners:</strong> For targeted ads, if legally permitted.</li>
                    </ul>
                </div>

                <!-- 7. Data Retention -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Data Retention</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li>We keep personal information as long as needed to fulfill stated purposes.</li>
                        <li>Transactional and accounting records are stored at least 7 years.</li>
                        <li>Marketing data is retained until you withdraw consent.</li>
                        <li>You may request deletion of personal data (see Section 10).</li>
                    </ul>
                </div>

                <!-- 8. Data Security -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Data Security</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">We apply industry-standard security measures, including:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 mb-4">
                        <li>SSL/TLS encryption for data in transit.</li>
                        <li>Secure servers, firewalls, and access restrictions.</li>
                        <li>Limited employee access to sensitive data.</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed">
                        However, no online transmission is 100% secure. You acknowledge the risk when using our Services.
                    </p>
                </div>

                <!-- 9. International Data Transfers -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">9. International Data Transfers</h2>
                    <p class="text-gray-700 leading-relaxed">
                        As a U.S.-based company, your data may be processed in the United States or other countries where our partners operate. By using our Services, you consent to such transfers. For EU users, safeguards such as Standard Contractual Clauses (SCCs) may apply.
                    </p>
                </div>

                <!-- 10. Your Rights -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Your Rights</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">Depending on your jurisdiction (e.g., GDPR in EU, CCPA in California), you may have the following rights:</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-1 mb-4">
                        <li><strong>Access:</strong> Obtain a copy of your data.</li>
                        <li><strong>Correction:</strong> Request updates or corrections.</li>
                        <li><strong>Deletion:</strong> Request deletion ("right to be forgotten").</li>
                        <li><strong>Restriction:</strong> Limit processing in certain cases.</li>
                        <li><strong>Data Portability:</strong> Receive your data in a structured, portable format.</li>
                        <li><strong>Opt-Out:</strong> Withdraw consent to marketing.</li>
                        <li><strong>Non-Discrimination:</strong> CCPA users will not face discrimination for exercising rights.</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed">
                        To exercise your rights, email us at <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a>.
                    </p>
                </div>

                <!-- 11. Children's Privacy -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Children's Privacy</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Our Services are not intended for individuals under 18. We do not knowingly collect data from minors. If a parent/guardian believes a child has provided personal information, contact us and we will delete it.
                    </p>
                </div>

                <!-- 12. Third-Party Links -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Third-Party Links</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Our website may link to external websites. We are not responsible for their privacy practices or content. Please review their privacy policies.
                    </p>
                </div>

                <!-- 13. Marketing Communications -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Marketing Communications</h2>
                    <ul class="list-disc list-inside text-gray-700 space-y-1">
                        <li>By submitting your email, you may receive promotional content.</li>
                        <li>You may unsubscribe anytime by clicking "unsubscribe" or contacting us.</li>
                        <li>We may still send non-promotional transactional emails.</li>
                    </ul>
                </div>

                <!-- 14. Automated Decision-Making -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">14. Automated Decision-Making</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We do not use personal data for fully automated decision-making that produces legal or significant effects (as defined under GDPR).
                    </p>
                </div>

                <!-- 15. Data Breach Notification -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">15. Data Breach Notification</h2>
                    <p class="text-gray-700 leading-relaxed">
                        If a data breach occurs, we will notify affected users and regulators (where legally required) within the applicable timeframe.
                    </p>
                </div>

                <!-- 16. Updates to Privacy Policy -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">16. Updates to Privacy Policy</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We may update this Policy at any time. Updates will be posted on our website with the revised "Effective Date." We encourage periodic review.
                    </p>
                </div>

                <!-- 17. Contact Us -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">17. Contact Us</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">For questions, concerns, or privacy-related requests:</p>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <p class="font-semibold text-gray-900">Skillio LLC</p>
                        <p class="text-gray-700">30 N Gould St, Sheridan, WY 82801</p>
                        <p class="text-gray-700 mt-2">
                            <strong>Email:</strong> 
                            <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700">info@skillio.pro</a>
                        </p>
                        <p class="text-gray-700 mt-4 text-sm">
                            If you are located in the EU/EEA, you may also have the right to lodge a complaint with your local Data Protection Authority.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
