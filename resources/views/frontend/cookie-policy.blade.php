@extends('frontend.layouts.app')

@section('title', 'Cookie Policy - Skillio')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-5">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Cookie Policy</h1>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="font-medium">Effective Date:</span>
                    <span>September 10, 2025</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            
            <!-- Introduction -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Introduction</h2>
                <p class="text-gray-700 leading-relaxed">
                    This Cookie Policy explains how Skillio LLC ("we," "our," "us") uses cookies and similar technologies on 
                    <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">https://skillio.pro</a> 
                    (the "Website"). By continuing to browse or use our website, you consent to the use of cookies as described here, 
                    unless you adjust your browser or cookie settings to disable them.
                </p>
            </div>

            <!-- Section 2: What Are Cookies? -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. What Are Cookies?</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Cookies are small text files placed on your device (computer, tablet, smartphone) by websites you visit. 
                    They perform functions such as:
                </p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2 mb-4">
                    <li>Recognizing your device and browser.</li>
                    <li>Storing user preferences.</li>
                    <li>Enabling secure logins and transactions.</li>
                    <li>Collecting analytics and traffic data.</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    We also use similar technologies including tracking pixels, beacons, and scripts (collectively referred to as "Cookies").
                </p>
            </div>

            <!-- Section 3: Types of Cookies We Use -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Types of Cookies We Use</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">3.1. Strictly Necessary Cookies</h3>
                        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1">
                            <li>Required for the basic operation of the Website.</li>
                            <li>Enable login, shopping cart, and secure payment processing.</li>
                            <li>Cannot be disabled through our cookie banner.</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">3.2. Performance & Analytics Cookies</h3>
                        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1">
                            <li>Collect anonymous data about Website use.</li>
                            <li>Help us improve speed, navigation, and content.</li>
                            <li>Examples: Google Analytics, Hotjar.</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">3.3. Functionality Cookies</h3>
                        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1">
                            <li>Remember language, location, and customization preferences.</li>
                            <li>Provide enhanced user experience (e.g., saved logins).</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">3.4. Advertising & Targeting Cookies</h3>
                        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1">
                            <li>Track browsing habits to deliver personalized ads.</li>
                            <li>Used for retargeting and campaign measurement.</li>
                            <li>Examples: Google Ads, Facebook Pixel, TikTok Pixel.</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">3.5. Social Media Cookies</h3>
                        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1">
                            <li>Enable sharing content directly to platforms like Facebook, Instagram, LinkedIn, TikTok, and YouTube.</li>
                            <li>May continue tracking your browsing even outside of our website.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Section 4: Third-Party Cookies -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Third-Party Cookies</h2>
                <p class="text-gray-700 leading-relaxed mb-4">We work with third-party providers who may place cookies on your device, including:</p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2 mb-4">
                    <li><strong>Analytics providers:</strong> Google Analytics, Hotjar.</li>
                    <li><strong>Advertising platforms:</strong> Meta Ads, Google Ads, TikTok Ads.</li>
                    <li><strong>Payment processors:</strong> Stripe, PayPal, Wise (for secure checkout sessions).</li>
                    <li><strong>Affiliate networks:</strong> For tracking referrals and commissions.</li>
                </ul>
                <p class="text-gray-700 leading-relaxed mb-4">
                    We do not control third-party cookies. Please review their individual policies for more information:
                </p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1">
                    <li><a href="https://policies.google.com/privacy" class="text-violet-600 hover:text-violet-700 font-medium" target="_blank">Google Privacy Policy</a></li>
                    <li><a href="https://www.facebook.com/privacy/policy/" class="text-violet-600 hover:text-violet-700 font-medium" target="_blank">Meta (Facebook) Privacy Policy</a></li>
                    <li><a href="https://www.tiktok.com/legal/privacy-policy" class="text-violet-600 hover:text-violet-700 font-medium" target="_blank">TikTok Privacy Policy</a></li>
                    <li><a href="https://stripe.com/privacy" class="text-violet-600 hover:text-violet-700 font-medium" target="_blank">Stripe Privacy Policy</a></li>
                </ul>
            </div>

            <!-- Section 5: Cookie Consent -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Cookie Consent</h2>
                <div class="space-y-4">
                    <p class="text-gray-700 leading-relaxed">
                        <strong>5.1.</strong> On your first visit, a cookie banner will appear requesting consent.
                    </p>
                    <div>
                        <p class="text-gray-700 leading-relaxed mb-2"><strong>5.2.</strong> Options include:</p>
                        <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1 ml-4">
                            <li><strong>Accept All</strong> – allows all cookies.</li>
                            <li><strong>Reject Non-Essential</strong> – allows only strictly necessary cookies.</li>
                            <li><strong>Customize Settings</strong> – lets you choose categories.</li>
                        </ul>
                    </div>
                    <p class="text-gray-700 leading-relaxed">
                        <strong>5.3.</strong> You can withdraw or change your consent at any time in the Website's cookie settings.
                    </p>
                </div>
            </div>

            <!-- Section 6: Managing & Disabling Cookies -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Managing & Disabling Cookies</h2>
                <p class="text-gray-700 leading-relaxed mb-4">You can control cookies through your browser settings:</p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1 mb-4">
                    <li><strong>Google Chrome:</strong> Settings → Privacy and security → Cookies and other site data</li>
                    <li><strong>Mozilla Firefox:</strong> Settings → Privacy & Security → Cookies and Site Data</li>
                    <li><strong>Safari:</strong> Preferences → Privacy → Manage Website Data</li>
                    <li><strong>Microsoft Edge:</strong> Settings → Cookies and site permissions → Cookies and site data</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    <strong>Note:</strong> If you disable cookies, certain features (login, checkout, saved preferences) may not function properly.
                </p>
            </div>

            <!-- Section 7: Data Collected via Cookies -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Data Collected via Cookies</h2>
                <p class="text-gray-700 leading-relaxed mb-4">Cookies may collect information such as:</p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2 mb-4">
                    <li>IP address, browser type, operating system.</li>
                    <li>Referring websites and pages visited.</li>
                    <li>Click behavior, time spent, and session duration.</li>
                    <li>Interactions with advertisements and campaigns.</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    We do not store sensitive data such as passwords or payment details in cookies.
                </p>
            </div>

            <!-- Section 8: Duration of Cookies -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Duration of Cookies</h2>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                    <li><strong>Session cookies:</strong> Expire when you close your browser.</li>
                    <li><strong>Persistent cookies:</strong> Remain until manually deleted or expired (from 30 days to 2 years, depending on provider).</li>
                </ul>
            </div>

            <!-- Section 9: Legal Basis for Processing (GDPR Compliance) -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Legal Basis for Processing (GDPR Compliance)</h2>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2 mb-4">
                    <li><strong>Consent:</strong> Analytics, advertising, and social media cookies.</li>
                    <li><strong>Legitimate Interest:</strong> Strictly necessary cookies required for Website functionality.</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    EU/EEA users can manage cookie preferences directly from our consent banner to ensure GDPR compliance.
                </p>
            </div>

            <!-- Section 10: California Privacy Rights (CCPA Compliance) -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. California Privacy Rights (CCPA Compliance)</h2>
                <p class="text-gray-700 leading-relaxed mb-4">If you are a California resident, you have the right to:</p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2 mb-4">
                    <li>Opt-out of the sale or sharing of personal data.</li>
                    <li>Request details about categories of data collected.</li>
                    <li>Request deletion of cookie-related data.</li>
                </ul>
                <p class="text-gray-700 leading-relaxed">
                    To exercise these rights, email us at 
                    <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a>.
                </p>
            </div>

            <!-- Section 11: Third-Party Opt-Out Options -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Third-Party Opt-Out Options</h2>
                <p class="text-gray-700 leading-relaxed mb-4">You can also opt out directly with third-party providers:</p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-1">
                    <li><a href="https://adssettings.google.com/" class="text-violet-600 hover:text-violet-700 font-medium" target="_blank">Google Ads Settings</a></li>
                    <li><a href="https://www.facebook.com/ads/preferences/" class="text-violet-600 hover:text-violet-700 font-medium" target="_blank">Facebook Ad Preferences</a></li>
                    <li><a href="https://www.tiktok.com/safety/ads-and-data/" class="text-violet-600 hover:text-violet-700 font-medium" target="_blank">TikTok Ad Settings</a></li>
                    <li><a href="https://optout.networkadvertising.org/" class="text-violet-600 hover:text-violet-700 font-medium" target="_blank">Network Advertising Initiative (NAI)</a></li>
                </ul>
            </div>

            <!-- Section 12: Data Transfers -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Data Transfers</h2>
                <p class="text-gray-700 leading-relaxed">
                    As a U.S.-based company, cookie data may be processed in the United States or other jurisdictions where 
                    third-party providers operate. Safeguards such as Standard Contractual Clauses may apply for EU users.
                </p>
            </div>

            <!-- Section 13: Updates to This Cookie Policy -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Updates to This Cookie Policy</h2>
                <p class="text-gray-700 leading-relaxed">
                    We may update this Cookie Policy from time to time. Any changes will be published on this page with an 
                    updated Effective Date. We encourage you to review this Policy periodically.
                </p>
            </div>

            <!-- Section 14: Contact Us -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">14. Contact Us</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    If you have any questions about this Cookie Policy, contact us at:
                </p>
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
