@extends('frontend.layouts.app')

@section('title', 'Disclaimer & Legal Notice - Skillio')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-5">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Disclaimer & Legal Notice</h1>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="font-medium">Effective Date:</span>
                    <span>September 10, 2025</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
            
            <!-- Section 1: General Information -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">1. General Information</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>1.1.</strong> All content, products, and services provided on 
                        <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">Skillio.pro</a> 
                        (the "Website") are intended solely for educational and informational purposes.
                    </p>
                    <p><strong>1.2.</strong> Nothing on this Website constitutes financial, legal, investment, medical, psychological, tax, or professional business advice.</p>
                    <p><strong>1.3.</strong> By using our Website, you accept that Skillio LLC ("we," "our," "us") is not liable for any actions, losses, or damages arising from reliance on our content, courses, or products.</p>
                    <p><strong>1.4.</strong> Accessing and using our Website is done entirely at your own risk.</p>
                </div>
            </div>

            <!-- Section 2: No Guarantee of Results -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">2. No Guarantee of Results</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>2.1.</strong> We make no guarantee regarding income, profit, business growth, or specific results from the use of our digital products, consulting, or templates.</p>
                    <p><strong>2.2.</strong> Testimonials and case studies represent personal experiences of individual users and are not indicative of guaranteed or typical results.</p>
                    <p><strong>2.3.</strong> Any projections, forecasts, or estimated outcomes are purely illustrative and non-binding.</p>
                </div>
            </div>

            <!-- Section 3: Financial & Investment Disclaimer -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Financial & Investment Disclaimer</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>3.1.</strong> Educational content related to forex trading, crypto assets, NFTs, stock market investing, and real estate is for informational purposes only.</p>
                    <p><strong>3.2.</strong> We are not licensed financial advisors, brokers, dealers, or fiduciaries.</p>
                    <p><strong>3.3.</strong> Investments and trading involve significant risk, including total loss of capital.</p>
                    <p><strong>3.4.</strong> Users should always consult a licensed financial advisor, attorney, or tax consultant before making financial decisions.</p>
                    <p><strong>3.5.</strong> Past performance or examples should never be interpreted as guarantees of future results.</p>
                </div>
            </div>

            <!-- Section 4: Legal & Tax Disclaimer -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Legal & Tax Disclaimer</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>4.1.</strong> We are not attorneys, CPAs, or licensed tax professionals.</p>
                    <p><strong>4.2.</strong> Any reference to legal structures, company formation, compliance strategies, or tax considerations is for educational purposes only.</p>
                    <p><strong>4.3.</strong> Laws and regulations vary by jurisdiction and may change over time; we are not responsible for outdated or inaccurate information.</p>
                    <p><strong>4.4.</strong> Users are responsible for ensuring their actions are compliant with applicable federal, state, and international laws.</p>
                </div>
            </div>

            <!-- Section 5: Marketing & Business Disclaimer -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Marketing & Business Disclaimer</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>5.1.</strong> Business-related content (advertising, affiliate marketing, SEO, email marketing, appointment setting, etc.) does not guarantee sales, conversions, or client acquisition.</p>
                    <p><strong>5.2.</strong> Results depend on multiple variables including execution, market demand, advertising budget, and personal business skills.</p>
                    <p><strong>5.3.</strong> Any earning potential examples are for motivational and educational purposes only and should not be taken as promises.</p>
                </div>
            </div>

            <!-- Section 6: Creative & Technical Disclaimer -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Creative & Technical Disclaimer</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>6.1.</strong> Guidance on design, coding, app development, AI automation, API integrations, and related tutorials is provided "as is."</p>
                    <p><strong>6.2.</strong> We do not warrant that any methods, templates, or codes will function as intended in all environments.</p>
                    <p><strong>6.3.</strong> Compatibility with third-party tools or platforms (e.g., YouTube, Shopify, Stripe, Meta Ads, TikTok) is not guaranteed.</p>
                </div>
            </div>

            <!-- Section 7: Adult & Sensitive Content Disclaimer -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Adult & Sensitive Content Disclaimer</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>7.1.</strong> Educational materials covering adult marketing strategies are strictly informational.</p>
                    <p><strong>7.2.</strong> We do not endorse, encourage, or facilitate unlawful, harmful, or exploitative content.</p>
                    <p><strong>7.3.</strong> Users must ensure compliance with all applicable laws, platform policies, and age restrictions.</p>
                    <p><strong>7.4.</strong> Skillio LLC disclaims liability for any misuse of these materials.</p>
                </div>
            </div>

            <!-- Section 8: Third-Party Tools & Links -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Third-Party Tools & Links</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>8.1.</strong> References to third-party platforms (e.g., Shopify, PayPal, Zapier, Amazon FBA, Webflow, etc.) are for educational purposes only.</p>
                    <p><strong>8.2.</strong> We do not control, endorse, or guarantee the availability, accuracy, or policies of any external platform.</p>
                    <p><strong>8.3.</strong> Use of third-party tools is at your own discretion and subject to their respective terms.</p>
                </div>
            </div>

            <!-- Section 9: Intellectual Property -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Intellectual Property</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>9.1.</strong> All Website content (including text, graphics, videos, templates, and digital downloads) is the intellectual property of Skillio LLC, unless otherwise stated.</p>
                    <p><strong>9.2.</strong> Unauthorized reproduction, distribution, resale, or misuse of our content is strictly prohibited.</p>
                    <p><strong>9.3.</strong> Users found violating intellectual property rights may be subject to civil and criminal liability.</p>
                </div>
            </div>

            <!-- Section 10: Limitation of Liability -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Limitation of Liability</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>10.1.</strong> To the maximum extent permitted by law, Skillio LLC disclaims all liability for damages (direct, indirect, incidental, or consequential) resulting from the use of our Website or Services.</p>
                    <p><strong>10.2.</strong> This includes, without limitation, financial loss, business interruptions, reputational harm, or data loss.</p>
                    <p><strong>10.3.</strong> Users accept full responsibility for decisions made based on our content.</p>
                </div>
            </div>

            <!-- Section 11: Indemnification -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Indemnification</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    By using our Website, you agree to indemnify, defend, and hold harmless Skillio LLC, its employees, contractors, and affiliates from any claims, damages, liabilities, costs, or expenses arising from:
                </p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                    <li>Your misuse of the Website or Services.</li>
                    <li>Your violation of these terms or any applicable law.</li>
                    <li>Your reliance on any content from 
                        <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">Skillio.pro</a>.
                    </li>
                </ul>
            </div>

            <!-- Section 12: Jurisdiction & Governing Law -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Jurisdiction & Governing Law</h2>
                <div class="space-y-4 text-gray-700 leading-relaxed">
                    <p><strong>12.1.</strong> This Disclaimer shall be governed by the laws of the State of Wyoming, United States.</p>
                    <p><strong>12.2.</strong> Any disputes shall be subject to the exclusive jurisdiction of courts located in Sheridan County, Wyoming.</p>
                </div>
            </div>

            <!-- Section 13: Severability -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">13. Severability</h2>
                <p class="text-gray-700 leading-relaxed">
                    If any provision of this Disclaimer & Legal Notice is found unenforceable, the remaining provisions shall remain valid and enforceable to the fullest extent permitted by law.
                </p>
            </div>

            <!-- Section 14: Updates to This Disclaimer -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">14. Updates to This Disclaimer</h2>
                <p class="text-gray-700 leading-relaxed">
                    We reserve the right to amend this Disclaimer at any time without prior notice. The most recent version will always be available on this page with the updated Effective Date.
                </p>
            </div>

            <!-- Section 15: Contact Us -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">15. Contact Us</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    For questions about this Disclaimer & Legal Notice, contact us at:
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
