@extends('frontend.layouts.app')

@section('title', 'Privacy Policy - Skillio')

@section('content')
<div class="min-h-screen bg-gray-50 pt-32 pb-5">
    <div class="max-w-[1400px] mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Privacy Policy</h1>
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
                        Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your
                        information when you use our website 
                        <a href="https://skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">skillio.pro</a> 
                        and related Services.
                    </p>
                </div>

                <!-- Section 1: Information We Collect -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Information We Collect</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">We collect:</p>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li><strong>Account Information:</strong> Name, email address</li>
                        <li><strong>Payment Information:</strong> Processed securely by third-party providers (we do not store credit card numbers)</li>
                        <li><strong>User Content:</strong> Comments, reviews, and messages you submit</li>
                    </ul>
                    <p class="text-gray-700 leading-relaxed mt-4">
                        We do not use cookies or tracking pixels at this time.
                    </p>
                </div>

                <!-- Section 2: How We Use Your Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">2. How We Use Your Information</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">We use your information to:</p>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>Create and manage your account</li>
                        <li>Provide educational services</li>
                        <li>Process payments and issue refunds when applicable</li>
                        <li>Communicate with you about updates, promotions, or support</li>
                    </ul>
                </div>

                <!-- Section 3: Sharing of Information -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Sharing of Information</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        We do not sell your personal data. We may share limited information with:
                    </p>
                    <ul class="list-disc list-inside text-gray-700 leading-relaxed space-y-2">
                        <li>Payment processors for transaction purposes</li>
                        <li>Service providers who help us operate the platform</li>
                        <li>Law enforcement, if required by law</li>
                    </ul>
                </div>

                <!-- Section 4: Data Storage & Security -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Data Storage & Security</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We store your data securely and use encryption where appropriate.
                    </p>
                </div>

                <!-- Section 5: International Users -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">5. International Users</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Your information may be stored and processed in the United States. By using our Services, you consent
                        to this transfer.
                    </p>
                </div>

                <!-- Section 6: Your Rights -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Your Rights</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Depending on your location, you may have the right to access, correct, or delete your personal data.
                        Contact us at 
                        <a href="mailto:info@skillio.pro" class="text-violet-600 hover:text-violet-700 font-medium">info@skillio.pro</a> 
                        to make such requests.
                    </p>
                </div>

                <!-- Section 7: Children's Privacy -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Children's Privacy</h2>
                    <p class="text-gray-700 leading-relaxed">
                        While we do not have age restrictions, our Services are not intended for unsupervised use by minors
                        without parental or guardian consent.
                    </p>
                </div>

                <!-- Section 8: Changes to This Policy -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Changes to This Policy</h2>
                    <p class="text-gray-700 leading-relaxed">
                        We may update this Privacy Policy at any time. Changes will be posted on this page with a revised "Last
                        Updated" date.
                    </p>
                </div>

                <!-- Section 9: Contact Us -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Contact Us</h2>
                    <p class="text-gray-700 leading-relaxed mb-4">
                        If you have any questions about this Privacy Policy, contact us at:
                    </p>
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="text-gray-700 leading-relaxed">
                            <p class="font-semibold">Skillio LLC</p>
                            <p>30 N Gould St Ste R</p>
                            <p>Sheridan, WY, 82801, USA</p>
                            <p class="mt-2">
                                Email: 
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
